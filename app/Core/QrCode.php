<?php
namespace App\Core;

/**
 * Trình tạo mã QR chuẩn ISO/IEC 18004 (Byte mode, ECC Level L, Version 1..12) thuần PHP
 * Xuất trực tiếp thẻ <svg> nội tuyến, không gửi secret 2FA ra bất kỳ API bên ngoài nào.
 */
class QrCode {
    private const ECC_LEVEL_L_BITS = 0b01;

    /**
     * Bảng thông số ECC Level L cho Version 1..12:
     * [ecPerBlock, g1Blocks, g1DataCw, g2Blocks, g2DataCw]
     */
    private const VERSION_PARAMS_L = [
        1  => [7,  1, 19, 0, 0],
        2  => [10, 1, 34, 0, 0],
        3  => [15, 1, 55, 0, 0],
        4  => [20, 1, 80, 0, 0],
        5  => [26, 1, 108, 0, 0],
        6  => [18, 2, 68, 0, 0],
        7  => [20, 2, 78, 0, 0],
        8  => [24, 2, 97, 0, 0],
        9  => [30, 2, 116, 0, 0],
        10 => [18, 2, 68, 2, 69],
        11 => [20, 4, 81, 0, 0],
        12 => [24, 2, 92, 2, 93],
    ];

    private const ALIGNMENT_POSITIONS = [
        1  => [],
        2  => [6, 18],
        3  => [6, 22],
        4  => [6, 26],
        5  => [6, 30],
        6  => [6, 34],
        7  => [6, 22, 38],
        8  => [6, 24, 42],
        9  => [6, 26, 46],
        10 => [6, 28, 50],
        11 => [6, 30, 54],
        12 => [6, 32, 58],
    ];

    /**
     * Trả về chuỗi SVG hoàn chỉnh cho dữ liệu đầu vào.
     */
    public static function svg(string $text, int $size = 200, int $quietZone = 3): string {
        $matrix = self::encodeMatrix($text);
        $dim = count($matrix);
        $viewDim = $dim + ($quietZone * 2);

        $paths = [];
        for ($y = 0; $y < $dim; $y++) {
            $x = 0;
            while ($x < $dim) {
                if (!$matrix[$y][$x]) {
                    $x++;
                    continue;
                }
                $runStart = $x;
                while ($x < $dim && $matrix[$y][$x]) {
                    $x++;
                }
                $runLen = $x - $runStart;
                $px = $runStart + $quietZone;
                $py = $y + $quietZone;
                $paths[] = "M{$px},{$py}h{$runLen}v1h-{$runLen}z";
            }
        }

        $pathData = implode('', $paths);
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $viewDim . ' ' . $viewDim . '" width="' . $size . '" height="' . $size . '" shape-rendering="crispEdges" role="img" aria-label="Mã QR thiết lập xác thực 2 bước">'
            . '<rect width="' . $viewDim . '" height="' . $viewDim . '" fill="#ffffff"/>'
            . '<path d="' . $pathData . '" fill="#0f172a"/>'
            . '</svg>';
    }

    /**
     * Mã hóa dữ liệu thành ma trận vuông bool[y][x].
     *
     * @return array<int, array<int, bool>>
     */
    public static function encodeMatrix(string $text): array {
        $bytes = array_values(unpack('C*', $text) ?: []);
        $byteLen = count($bytes);
        $version = self::selectVersion($byteLen);

        [$ecPerBlock, $g1Blocks, $g1DataCw, $g2Blocks, $g2DataCw] = self::VERSION_PARAMS_L[$version];
        $totalDataCw = ($g1Blocks * $g1DataCw) + ($g2Blocks * $g2DataCw);

        // 1. Tạo chuỗi bit dữ liệu (Byte Mode = 0100)
        $bits = '0100';
        $countBitsLen = $version <= 9 ? 8 : 16;
        $bits .= str_pad(decbin($byteLen), $countBitsLen, '0', STR_PAD_LEFT);
        foreach ($bytes as $b) {
            $bits .= str_pad(decbin($b), 8, '0', STR_PAD_LEFT);
        }

        $maxDataBits = $totalDataCw * 8;
        $terminatorLen = min(4, $maxDataBits - strlen($bits));
        if ($terminatorLen > 0) {
            $bits .= str_repeat('0', $terminatorLen);
        }
        $rem = strlen($bits) % 8;
        if ($rem !== 0) {
            $bits .= str_repeat('0', 8 - $rem);
        }

        $dataCodewords = [];
        foreach (str_split($bits, 8) as $byteStr) {
            if ($byteStr !== '') {
                $dataCodewords[] = bindec($byteStr);
            }
        }

        $padBytes = [0xEC, 0x11];
        $padIdx = 0;
        while (count($dataCodewords) < $totalDataCw) {
            $dataCodewords[] = $padBytes[$padIdx % 2];
            $padIdx++;
        }

        // 2. Chia block và tính Reed-Solomon ECC rồi xen kẽ (interleave)
        $codewords = self::interleaveWithEcc($dataCodewords, $ecPerBlock, $g1Blocks, $g1DataCw, $g2Blocks, $g2DataCw);

        // 3. Dựng khung ma trận và các pattern chức năng
        $size = 17 + (4 * $version);
        $modules = array_fill(0, $size, array_fill(0, $size, false));
        $isFunction = array_fill(0, $size, array_fill(0, $size, false));

        self::drawFinderPatterns($modules, $isFunction, $size);
        self::drawAlignmentPatterns($modules, $isFunction, $version);
        self::drawTimingPatterns($modules, $isFunction, $size);
        self::reserveFormatAndVersionAreas($modules, $isFunction, $version, $size);

        // 4. Đặt bit dữ liệu + ECC theo đường zig-zag
        self::placeCodewords($modules, $isFunction, $codewords, $size);

        // 5. Chọn Mask 0..7 có điểm phạt thấp nhất
        $bestMask = 0;
        $bestPenalty = PHP_INT_MAX;
        $bestModules = $modules;

        for ($mask = 0; $mask < 8; $mask++) {
            $candidate = self::applyMask($modules, $isFunction, $mask, $size);
            self::drawFormatBits($candidate, $mask, $size);
            if ($version >= 7) {
                self::drawVersionBits($candidate, $version, $size);
            }
            $penalty = self::calculatePenalty($candidate, $size);
            if ($penalty < $bestPenalty) {
                $bestPenalty = $penalty;
                $bestMask = $mask;
                $bestModules = $candidate;
            }
        }

        return $bestModules;
    }

    private static function selectVersion(int $byteLen): int {
        foreach (self::VERSION_PARAMS_L as $ver => $params) {
            [, $g1Blocks, $g1Cw, $g2Blocks, $g2Cw] = $params;
            $capacityBytes = ($g1Blocks * $g1Cw) + ($g2Blocks * $g2Cw);
            $headerBits = 4 + ($ver <= 9 ? 8 : 16);
            $usableBytes = (int)floor((($capacityBytes * 8) - $headerBits) / 8);
            if ($byteLen <= $usableBytes) {
                return $ver;
            }
        }
        throw new \InvalidArgumentException('Dữ liệu QR quá dài so với giới hạn Version 12.');
    }

    /**
     * @param array<int, int> $dataCodewords
     * @return array<int, int>
     */
    private static function interleaveWithEcc(
        array $dataCodewords,
        int $ecPerBlock,
        int $g1Blocks,
        int $g1DataCw,
        int $g2Blocks,
        int $g2DataCw
    ): array {
        $genPoly = self::rsGeneratorPoly($ecPerBlock);
        $blocksData = [];
        $blocksEc = [];
        $offset = 0;

        for ($b = 0; $b < $g1Blocks + $g2Blocks; $b++) {
            $len = $b < $g1Blocks ? $g1DataCw : $g2DataCw;
            $slice = array_slice($dataCodewords, $offset, $len);
            $offset += $len;
            $blocksData[] = $slice;
            $blocksEc[] = self::rsComputeRemainder($slice, $genPoly);
        }

        $result = [];
        $maxDataLen = max($g1DataCw, $g2DataCw);
        for ($i = 0; $i < $maxDataLen; $i++) {
            foreach ($blocksData as $block) {
                if (isset($block[$i])) {
                    $result[] = $block[$i];
                }
            }
        }
        for ($i = 0; $i < $ecPerBlock; $i++) {
            foreach ($blocksEc as $ec) {
                $result[] = $ec[$i];
            }
        }
        return $result;
    }

    /**
     * @return array<int, int>
     */
    private static function rsGeneratorPoly(int $degree): array {
        $poly = [1];
        for ($i = 0; $i < $degree; $i++) {
            $root = self::gfPow2($i);
            $next = array_fill(0, count($poly) + 1, 0);
            for ($j = 0; $j < count($poly); $j++) {
                $next[$j] ^= $poly[$j];
                $next[$j + 1] ^= self::gfMultiply($poly[$j], $root);
            }
            $poly = $next;
        }
        return $poly;
    }

    /**
     * @param array<int, int> $data
     * @param array<int, int> $genPoly
     * @return array<int, int>
     */
    private static function rsComputeRemainder(array $data, array $genPoly): array {
        $ecLen = count($genPoly) - 1;
        $rem = array_fill(0, $ecLen, 0);
        foreach ($data as $byte) {
            $factor = $byte ^ $rem[0];
            array_shift($rem);
            $rem[] = 0;
            if ($factor !== 0) {
                for ($i = 0; $i < $ecLen; $i++) {
                    $rem[$i] ^= self::gfMultiply($genPoly[$i + 1], $factor);
                }
            }
        }
        return $rem;
    }

    private static function gfPow2(int $exp): int {
        $val = 1;
        for ($i = 0; $i < $exp; $i++) {
            $val <<= 1;
            if (($val & 0x100) !== 0) {
                $val ^= 0x11D;
            }
        }
        return $val;
    }

    private static function gfMultiply(int $x, int $y): int {
        $z = 0;
        for ($bit = 7; $bit >= 0; $bit--) {
            $z = ($z << 1) ^ (($z >> 7) * 0x11D);
            if ((($y >> $bit) & 1) !== 0) {
                $z ^= $x;
            }
        }
        return $z & 0xFF;
    }

    private static function drawFinderPatterns(array &$modules, array &$isFunction, int $size): void {
        $origins = [[0, 0], [$size - 7, 0], [0, $size - 7]];
        foreach ($origins as [$ox, $oy]) {
            for ($dy = -1; $dy <= 7; $dy++) {
                for ($dx = -1; $dx <= 7; $dx++) {
                    $x = $ox + $dx;
                    $y = $oy + $dy;
                    if ($x < 0 || $x >= $size || $y < 0 || $y >= $size) {
                        continue;
                    }
                    $dist = max(abs($dx - 3), abs($dy - 3));
                    $dark = ($dx >= 0 && $dx <= 6 && $dy >= 0 && $dy <= 6) && ($dist === 0 || $dist === 1 || $dist === 3);
                    $modules[$y][$x] = $dark;
                    $isFunction[$y][$x] = true;
                }
            }
        }
    }

    private static function drawAlignmentPatterns(array &$modules, array &$isFunction, int $version): void {
        $coords = self::ALIGNMENT_POSITIONS[$version];
        foreach ($coords as $cy) {
            foreach ($coords as $cx) {
                // Bỏ qua 3 góc có Finder Pattern
                if ($isFunction[$cy][$cx]) {
                    continue;
                }
                for ($dy = -2; $dy <= 2; $dy++) {
                    for ($dx = -2; $dx <= 2; $dx++) {
                        $dist = max(abs($dx), abs($dy));
                        $modules[$cy + $dy][$cx + $dx] = ($dist === 0 || $dist === 2);
                        $isFunction[$cy + $dy][$cx + $dx] = true;
                    }
                }
            }
        }
    }

    private static function drawTimingPatterns(array &$modules, array &$isFunction, int $size): void {
        for ($i = 8; $i < $size - 8; $i++) {
            $dark = ($i % 2 === 0);
            if (!$isFunction[6][$i]) {
                $modules[6][$i] = $dark;
                $isFunction[6][$i] = true;
            }
            if (!$isFunction[$i][6]) {
                $modules[$i][6] = $dark;
                $isFunction[$i][6] = true;
            }
        }
    }

    private static function reserveFormatAndVersionAreas(array &$modules, array &$isFunction, int $version, int $size): void {
        for ($i = 0; $i <= 8; $i++) {
            if ($i !== 6) {
                $isFunction[8][$i] = true;
                $isFunction[$i][8] = true;
            }
        }
        for ($i = $size - 8; $i < $size; $i++) {
            $isFunction[8][$i] = true;
            $isFunction[$i][8] = true;
        }
        // Dark module cố định ở (8, 4*version + 9)
        $modules[$size - 8][8] = true;
        $isFunction[$size - 8][8] = true;

        if ($version >= 7) {
            for ($i = 0; $i < 6; $i++) {
                for ($j = $size - 11; $j < $size - 8; $j++) {
                    $isFunction[$i][$j] = true;
                    $isFunction[$j][$i] = true;
                }
            }
        }
    }

    private static function placeCodewords(array &$modules, array $isFunction, array $codewords, int $size): void {
        $totalBits = count($codewords) * 8;
        $bitIndex = 0;

        for ($right = $size - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right = 5;
            }
            for ($vert = 0; $vert < $size; $vert++) {
                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;
                    $upward = ((($right + 1) & 2) === 0);
                    $y = $upward ? ($size - 1 - $vert) : $vert;
                    if ($isFunction[$y][$x]) {
                        continue;
                    }
                    $dark = false;
                    if ($bitIndex < $totalBits) {
                        $cw = $codewords[(int)($bitIndex >> 3)];
                        $dark = (($cw >> (7 - ($bitIndex & 7))) & 1) !== 0;
                        $bitIndex++;
                    }
                    $modules[$y][$x] = $dark;
                }
            }
        }
    }

    private static function applyMask(array $modules, array $isFunction, int $mask, int $size): array {
        $out = $modules;
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($isFunction[$y][$x]) {
                    continue;
                }
                $invert = match ($mask) {
                    0 => (($x + $y) % 2) === 0,
                    1 => ($y % 2) === 0,
                    2 => ($x % 3) === 0,
                    3 => (($x + $y) % 3) === 0,
                    4 => (((int)floor($y / 2) + (int)floor($x / 3)) % 2) === 0,
                    5 => (($x * $y) % 2) + (($x * $y) % 3) === 0,
                    6 => (((($x * $y) % 2) + (($x * $y) % 3)) % 2) === 0,
                    7 => (((($x + $y) % 2) + (($x * $y) % 3)) % 2) === 0,
                    default => false,
                };
                if ($invert) {
                    $out[$y][$x] = !$out[$y][$x];
                }
            }
        }
        return $out;
    }

    private static function drawFormatBits(array &$modules, int $mask, int $size): void {
        $data = (self::ECC_LEVEL_L_BITS << 3) | $mask;
        $rem = $data;
        for ($i = 0; $i < 10; $i++) {
            $rem = ($rem << 1) ^ ((($rem >> 9) & 1) * 0x537);
        }
        $bits = (($data << 10) | $rem) ^ 0x5412;

        // Bản 1 (góc trên-trái): MSB bit 14 ở (x=0, y=8) .. bit 0 ở (x=8, y=0)
        for ($i = 0; $i <= 5; $i++) {
            $modules[8][$i] = (($bits >> (14 - $i)) & 1) !== 0;
        }
        $modules[8][7] = (($bits >> 8) & 1) !== 0;
        $modules[8][8] = (($bits >> 7) & 1) !== 0;
        $modules[7][8] = (($bits >> 6) & 1) !== 0;
        for ($i = 0; $i <= 5; $i++) {
            $modules[5 - $i][8] = (($bits >> (5 - $i)) & 1) !== 0;
        }

        // Bản 2: 7 bit (14..8) ở cột x=8 góc dưới-trái + Dark module cố định + 8 bit (7..0) ở hàng y=8 góc trên-phải
        for ($i = 0; $i < 7; $i++) {
            $modules[$size - 1 - $i][8] = (($bits >> (14 - $i)) & 1) !== 0;
        }
        $modules[$size - 8][8] = true;
        for ($i = 0; $i < 8; $i++) {
            $modules[8][$size - 8 + $i] = (($bits >> (7 - $i)) & 1) !== 0;
        }
    }

    private static function drawVersionBits(array &$modules, int $version, int $size): void {
        $rem = $version;
        for ($i = 0; $i < 12; $i++) {
            $rem = ($rem << 1) ^ ((($rem >> 11) & 1) * 0x1F25);
        }
        $bits = ($version << 12) | $rem;

        for ($i = 0; $i < 18; $i++) {
            $dark = (($bits >> $i) & 1) !== 0;
            $a = $size - 11 + ($i % 3);
            $b = (int)floor($i / 3);
            $modules[$b][$a] = $dark;
            $modules[$a][$b] = $dark;
        }
    }

    private static function calculatePenalty(array $modules, int $size): int {
        $penalty = 0;

        // N1: 5+ ô liên tiếp cùng màu theo hàng và cột
        for ($y = 0; $y < $size; $y++) {
            $runColor = $modules[$y][0];
            $runLen = 1;
            for ($x = 1; $x < $size; $x++) {
                if ($modules[$y][$x] === $runColor) {
                    $runLen++;
                } else {
                    if ($runLen >= 5) {
                        $penalty += 3 + ($runLen - 5);
                    }
                    $runColor = $modules[$y][$x];
                    $runLen = 1;
                }
            }
            if ($runLen >= 5) {
                $penalty += 3 + ($runLen - 5);
            }
        }
        for ($x = 0; $x < $size; $x++) {
            $runColor = $modules[0][$x];
            $runLen = 1;
            for ($y = 1; $y < $size; $y++) {
                if ($modules[$y][$x] === $runColor) {
                    $runLen++;
                } else {
                    if ($runLen >= 5) {
                        $penalty += 3 + ($runLen - 5);
                    }
                    $runColor = $modules[$y][$x];
                    $runLen = 1;
                }
            }
            if ($runLen >= 5) {
                $penalty += 3 + ($runLen - 5);
            }
        }

        // N2: Khối 2x2 cùng màu
        for ($y = 0; $y < $size - 1; $y++) {
            for ($x = 0; $x < $size - 1; $x++) {
                $c = $modules[$y][$x];
                if ($c === $modules[$y][$x + 1] && $c === $modules[$y + 1][$x] && $c === $modules[$y + 1][$x + 1]) {
                    $penalty += 3;
                }
            }
        }

        // N3: Mẫu tìm kiếm giả 1:1:3:1:1
        $darkCount = 0;
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($modules[$y][$x]) {
                    $darkCount++;
                }
                if ($x + 6 < $size) {
                    $isPat = $modules[$y][$x] && !$modules[$y][$x + 1] && $modules[$y][$x + 2] && $modules[$y][$x + 3] && $modules[$y][$x + 4] && !$modules[$y][$x + 5] && $modules[$y][$x + 6];
                    if ($isPat) {
                        $leftClear = $x >= 4 && !$modules[$y][$x - 1] && !$modules[$y][$x - 2] && !$modules[$y][$x - 3] && !$modules[$y][$x - 4];
                        $rightClear = $x + 10 < $size && !$modules[$y][$x + 7] && !$modules[$y][$x + 8] && !$modules[$y][$x + 9] && !$modules[$y][$x + 10];
                        if ($leftClear || $rightClear) {
                            $penalty += 40;
                        }
                    }
                }
                if ($y + 6 < $size) {
                    $isPatV = $modules[$y][$x] && !$modules[$y + 1][$x] && $modules[$y + 2][$x] && $modules[$y + 3][$x] && $modules[$y + 4][$x] && !$modules[$y + 5][$x] && $modules[$y + 6][$x];
                    if ($isPatV) {
                        $topClear = $y >= 4 && !$modules[$y - 1][$x] && !$modules[$y - 2][$x] && !$modules[$y - 3][$x] && !$modules[$y - 4][$x];
                        $botClear = $y + 10 < $size && !$modules[$y + 7][$x] && !$modules[$y + 8][$x] && !$modules[$y + 9][$x] && !$modules[$y + 10][$x];
                        if ($topClear || $botClear) {
                            $penalty += 40;
                        }
                    }
                }
            }
        }

        // N4: Tỷ lệ ô đen lệch khỏi 50%
        $totalModules = $size * $size;
        $k = (int)(abs(($darkCount * 20) - ($totalModules * 10)) / $totalModules);
        $penalty += $k * 10;

        return $penalty;
    }
}
