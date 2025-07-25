<?php
/**
 * Macro helpers
 */

/**
 * Locale macro
 * Generates the dropdown menu of available languages
 */
function locales($name = 'locale', $selected = null, $class = null, $id = null) {
    $idclause = (!is_null($id)) ? $id : '';

    $select = '<select name="'.$name.'" class="'.$class.'" style="min-width:100%"'.$idclause.' aria-label="'.$name.'" data-placeholder="'.trans('localizations.select_language').'">';
    $select .= '<option value="" role="option">'.trans('localizations.select_language').'</option>';

    foreach (trans('localizations.languages') as $abbr => $locale) {
        $select .= '<option value="'.$abbr.'"'.(($selected == $abbr) ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'>'.$locale.'</option> ';
    }

    $select .= '</select>';

    return $select;
}

/**
 * Country macro
 * Generates the dropdown menu of countries for the profile form
 */
function countries($name = 'country', $selected = null, $class = null, $id = null) {
    $idclause = (!is_null($id)) ? $id : '';

    $select = '<select name="'.$name.'" class="'.$class.'" style="width:100%" '.$idclause.' aria-label="'.$name.'" data-placeholder="'.trans('localizations.select_country').'">';
    $select .= '<option value="" role="option">'.trans('localizations.select_country').'</option>';

    foreach (trans('localizations.countries') as $abbr => $country) {
        if ($abbr != '') {
            $abbr = strtoupper($abbr);
        }
        $select .= '<option value="'.$abbr.'"'.(($selected == $abbr) ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'>'.$country.'</option> ';
    }

    $select .= '</select>';

    return $select;
}

/**
 * Date display format macro
 */
function date_display_format($name = 'date_display_format', $selected = null, $class = null) {
    $formats = [
        'Y-m-d',
        'D M d, Y',
        'M j, Y',
        'd M, Y',
        'm/d/Y',
        'n/d/y',
        'd/m/Y',
        'd.m.Y',
        'Y.m.d.',
    ];

    foreach ($formats as $format) {
        $date_display_formats[$format] = Carbon::parse(date('Y-m-d'))->format($format);
    }
    $select = '<select name="'.$name.'" class="'.$class.'" style="min-width:100%" aria-label="'.$name.'">';
    foreach ($date_display_formats as $format => $date_display_format) {
        $select .= '<option value="'.$format.'"'.($selected == $format ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'">'.$date_display_format.'</option> ';
    }

    $select .= '</select>';

    return $select;
}

/**
 * Time display format macro
 */
function time_display_format($name = 'time_display_format', $selected = null, $class = null) {
    $formats = [
        'g:iA',
        'h:iA',
        'H:i',
    ];

    foreach ($formats as $format) {
        $time_display_formats[$format] = Carbon::now()->format($format);
    }
    $select = '<select name="'.$name.'" class="'.$class.'" style="min-width:150px" aria-label="'.$name.'">';
    foreach ($time_display_formats as $format => $time_display_format) {
        $select .= '<option value="'.$format.'"'.($selected == $format ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'>'.$time_display_format.'</option> ';
    }

    $select .= '</select>';

    return $select;
}

/**
 * Digit separator macro
 */
function digit_separator($name = 'digit_separator', $selected = null, $class = null) {
    $formats = [
        '1,234.56',
        '1.234,56',
    ];

    $select = '<select name="'.$name.'" class="'.$class.'" style="min-width:120px">';
    foreach ($formats as $format_inner) {
        $select .= '<option value="'.$format_inner.'"'.($selected == $format_inner ? ' selected="selected"' : '').'>'.$format_inner.'</option> ';
    }

    $select .= '</select>';

    return $select;
}

/**
 * Name display format macro
 */
function name_display_format($name = 'name_display_format', $selected = null, $class = null) {
    $formats = [
        'first_last' => trans('general.firstname_lastname_display'),
        'last_first' => trans('general.lastname_firstname_display'),
    ];

    $select = '<select name="'.$name.'" class="'.$class.'" style="width: 100%" aria-label="'.$name.'">';
    foreach ($formats as $format => $label) {
        $select .= '<option value="'.$format.'"'.($selected == $format ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'>'.$label.'</option> '."\n";
    }

    $select .= '</select>';

    return $select;
}

/**
 * Barcode types macro (1D barcodes)
 */
function alt_barcode_types($name = 'alt_barcode', $selected = null, $class = null) {
    $barcode_types = [
        'C128',
        'C39',
        'PDF417',
        'EAN5',
        'EAN13',
        'UPCA',
        'UPCE',
    ];

    $select = '<select name="'.$name.'" class="'.$class.'" aria-label="'.$name.'">';
    foreach ($barcode_types as $barcode_type) {
        $select .= '<option value="'.$barcode_type.'"'.($selected == $barcode_type ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'>'.$barcode_type.'</option> ';
    }

    $select .= '</select>';

    return $select;
}

/**
 * Barcode types macro (2D barcodes)
 */
function barcode_types($name = 'barcode_type', $selected = null, $class = null) {
    $barcode_types = [
        'QRCODE',
        'DATAMATRIX',
    ];

    $select = '<select name="'.$name.'" class="'.$class.'" aria-label="'.$name.'">';
    foreach ($barcode_types as $barcode_type) {
        $select .= '<option value="'.$barcode_type.'"'.($selected == $barcode_type ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'>'.$barcode_type.'</option> ';
    }

    $select .= '</select>';

    return $select;
}

/**
 * Username format macro
 */
function username_format($name = 'username_format', $selected = null, $class = null) {
    $formats = [
        'firstname.lastname' => trans('general.firstname_lastname_format'),
        'firstname' => trans('general.first_name_format'),
        'filastname' => trans('general.filastname_format'),
        'lastnamefirstinitial' => trans('general.lastnamefirstinitial_format'),
        'firstname_lastname' => trans('general.firstname_lastname_underscore_format'),
        'firstinitial.lastname' => trans('general.firstinitial.lastname'),
        'lastname_firstinitial' => trans('general.lastname_firstinitial'),
        'firstnamelastname' => trans('general.firstnamelastname'),
        'firstnamelastinitial' => trans('general.firstnamelastinitial'),
        'lastname.firstname' => trans('general.lastnamefirstname'),
    ];

    $select = '<select name="'.$name.'" class="'.$class.'" style="width: 100%" aria-label="'.$name.'">';
    foreach ($formats as $format => $label) {
        $select .= '<option value="'.$format.'"'.($selected == $format ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'>'.$label.'</option> '."\n";
    }

    $select .= '</select>';

    return $select;
}

/**
 * Two factor options macro
 */
function two_factor_options($name = 'two_factor_enabled', $selected = null, $class = null) {
    $formats = [
        '' => trans('admin/settings/general.two_factor_disabled'),
        '1' => trans('admin/settings/general.two_factor_optional'),
        '2' => trans('admin/settings/general.two_factor_required'),
    ];

    $select = '<select name="'.$name.'" class="'.$class.'" aria-label="'.$name.'">';
    foreach ($formats as $format => $label) {
        $select .= '<option value="'.$format.'"'.($selected == $format ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'>'.$label.'</option> '."\n";
    }

    $select .= '</select>';

    return $select;
}

/**
 * Custom field elements macro
 */
function customfield_elements($name = 'customfield_elements', $selected = null, $class = null) {
    $formats = [
        'text' => 'Text Box',
        'listbox' => 'List Box',
        'textarea' => 'Textarea (multi-line)',
        'checkbox' => 'Checkbox',
        'radio' => 'Radio Buttons',
    ];

    $select = '<select name="'.$name.'" class="'.$class.'" style="width: 100%" aria-label="'.$name.'">';
    foreach ($formats as $format => $label) {
        $select .= '<option value="'.$format.'"'.($selected == $format ? ' selected="selected" role="option" aria-selected="true"' : ' aria-selected="false"').'>'.$label.'</option> '."\n";
    }

    $select .= '</select>';

    return $select;
}

/**
 * Skin macro
 */
function skin($name = 'skin', $selected = null, $class = null) {
    $formats = [
        'blue' => 'Default Blue',
        'blue-dark' => 'Blue (Dark Mode)',
        'green' => 'Green Dark',
        'green-dark' => 'Green (Dark Mode)',
        'red' => 'Red Dark',
        'red-dark' => 'Red (Dark Mode)',
        'orange' => 'Orange Dark',
        'orange-dark' => 'Orange (Dark Mode)',
        'black' => 'Black',
        'black-dark' => 'Black (Dark Mode)',
        'purple' => 'Purple',
        'purple-dark' => 'Purple (Dark Mode)',
        'yellow' => 'Yellow',
        'yellow-dark' => 'Yellow (Dark Mode)',
        'contrast' => 'High Contrast',
    ];

    $select = '<select name="'.$name.'" class="'.$class.'" style="width: 250px" aria-label="'.$name.'">';
    foreach ($formats as $format => $label) {
        $select .= '<option value="'.$format.'"'.($selected == $format ? ' selected="selected"' : '').'>'.$label.'</option> '."\n";
    }

    $select .= '</select>';

    return $select;
}

/**
 * User skin macro
 */
function user_skin($name = 'skin', $selected = null, $class = null) {
    $formats = [
        '' => 'Site Default',
        'blue' => 'Default Blue',
        'blue-dark' => 'Blue (Dark Mode)',
        'green' => 'Green Dark',
        'green-dark' => 'Green (Dark Mode)',
        'red' => 'Red Dark',
        'red-dark' => 'Red (Dark Mode)',
        'orange' => 'Orange Dark',
        'orange-dark' => 'Orange (Dark Mode)',
        'black' => 'Black',
        'black-dark' => 'Black (Dark Mode)',
        'purple' => 'Purple',
        'purple-dark' => 'Purple (Dark Mode)',
        'yellow' => 'Yellow',
        'yellow-dark' => 'Yellow (Dark Mode)',
        'contrast' => 'High Contrast',
    ];

    $select = '<select name="'.$name.'" class="'.$class.'" style="width: 250px">';
    foreach ($formats as $format => $label) {
        $select .= '<option value="'.$format.'"'.($selected == $format ? ' selected="selected"' : '').'>'.$label.'</option> '."\n";
    }

    $select .= '</select>';

    return $select;
}
