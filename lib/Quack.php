<?php

declare(strict_types=1);

class Quack {
    /*
            a a a a a
            c c c a a
            k k k c c
            . ! ? k k
                  ! .
        Qu  A B C D E
        Quu F G H I J
        Qo  K L M N O
        Qoo P Q R S T
        Qou U V W X Y
        Quo Z Ä Ö Ü ß
     */
    private array $chars = [
        'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y',
        'Z', 'Ä', 'Ö', 'Ü', 'ß',
    ];
    private array $codeX = [
        'Qu', 'Quu', 'Qo', 'Qoo', 'Qou', 'Quo',
    ];
    private array $codeY = [
        'ack.', 'ack!', 'ack?', 'aack!', 'aack.',
    ];

    private array $encodeTable;
    private array $decodeTable;

    public function __construct()
    {
        $this->createTables();
    }

    private function createTables(): void
    {
        $columns = ceil(count($this->chars) / count($this->codeX));
        $i = $columns + 1;
        foreach ($this->chars as $char) {
            if ($i > $columns) {
                $codeX = $this->getRotatingNext($this->codeX);
                $i = 1;
            }
            $codeY = $this->getRotatingNext($this->codeY);
            $this->encodeTable[$char] = $codeX.$codeY;
            $this->encodeTable[mb_strtolower($char)] = mb_strtolower($codeX.$codeY);
            $this->decodeTable[$codeX.$codeY] = $char;
            $this->decodeTable[mb_strtolower($codeX.$codeY)] = mb_strtolower($char);
            $i++;
        }
    }

    private function getRotatingNext(array &$array): mixed
    {
        $current = current($array);
        if (false === next($array)) {
            reset($array);
        }
        return $current;
    }

    public function encode(string $plain): string
    {
        $cipher = '';

        foreach (mb_str_split($plain) as $char) {
            if (!isset($this->encodeTable[$char])) {
                $cipher .= $char;
                continue;
            }

            if ('' !== $cipher) {
                $cipher .= ' ';
            }

            $cipher .= $this->encodeTable[$char];
        }

        return $cipher;
    }

    public function decode(string $cipher): string
    {
        $cipher = str_replace([' q', ' Q'], ['q', 'Q'], $cipher);
        return strtr($cipher, $this->decodeTable);
    }
}

/*
$q = new Quack();
echo $c=$q->encode('Der qäkende Frosch.');
echo "\n".$q->decode($c);
echo "\n\n";
echo $c=$q->encode('abcdefghijklmnopqrstuvwxyzäöüß');
echo "\n".$q->decode($c);
*/
