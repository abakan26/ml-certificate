<?php


class FontHandler
{
    public static function getFonts()
    {
        $fonts = [];
        foreach (glob(PLUGIN_PATH . '/assets/fonts/*', GLOB_ONLYDIR) as $font_dir) {
            $regular = basename(glob($font_dir . '/*-regular.ttf')[0]);
            $bold = basename(glob($font_dir . '/*-bold.ttf')[0]);
            if (!empty($regular)) {
                $fonts[basename($font_dir)]['R'] = $regular;
            }
            if (!empty($bold)) {
                $fonts[basename($font_dir)]['B'] = $bold;
            }
        }
        return $fonts;
    }

    public static function getDirs()
    {
        return glob(PLUGIN_PATH . '/assets/fonts/*', GLOB_ONLYDIR);
    }

    public static function getDefault()
    {
        return basename(self::getDirs()[0]);
    }

    public static function getFontFamilies()
    {
        return array_map(function ($fontDir) {
            return basename($fontDir);
        }, self::getDirs());
    }

    public static function hasWeight($name, $value)
    {
        return count(glob(PLUGIN_PATH . '/assets/fonts/' . $name . '/*-' . $value . '*'));
    }

    public static function generateDynamic()
    {
        ob_start();
        include PLUGIN_PATH . '/assets/fonts/fonts.php';
        return self::compressCssCode(ob_get_clean());
    }

    public static function compressCssCode($code)
    {
        // Remove Comments
        $code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code);
        // Remove tabs, spaces, newlines, etc.
        return str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $code);
    }
}
