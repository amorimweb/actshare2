<?php
/** Safe counterpart of the form toolbar's formatting for e-mail delivery. */
function formatarTexto(string $text): string {
    $inline = static function (string $line): string {
        $line = htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return preg_replace_callback('/\*\*([^*\n]+)\*\*|__([^_\n]+)__|~~([^~\n]+)~~|\*([^*\n]+)\*/u', static function ($m) {
            foreach ([1 => 'strong', 2 => 'u', 3 => 's', 4 => 'em'] as $i => $tag) {
                if (isset($m[$i]) && $m[$i] !== '') return "<$tag>{$m[$i]}</$tag>";
            }
            return $m[0];
        }, $line);
    };
    $html = ''; $list = null;
    foreach (explode("\n", str_replace("\r\n", "\n", $text)) as $i => $line) {
        if (preg_match('/^\s*(?:([-•])|\d+\.)\s+(.+)$/u', $line, $m)) {
            $tag = !empty($m[1]) ? 'ul' : 'ol';
            if ($list !== $tag) {
                if ($list) $html .= "</$list>";
                $html .= "<$tag>"; $list = $tag;
            }
            $html .= '<li>' . $inline($m[2]) . '</li>';
        } else {
            if ($list) { $html .= "</$list>"; $list = null; }
            elseif ($i) $html .= '<br>';
            $html .= $inline($line);
        }
    }
    return $html . ($list ? "</$list>" : '');
}
