<?php
//---------- Initialize
function InitializeForm($arrControlNames)
{
    $arrInit = array();
    foreach($arrControlNames as $controlName)
    {
        $arrInit[$controlName] = isset($_POST[$controlName]) ? $_POST[$controlName] : "";
    }
    
    return $arrInit;
}

//---------- code sanitization and cleaning
function SanitizePostVariables($name, $type, $default)
{
    $var = isset($_POST[$name]) ? filter_input(INPUT_POST, $name, SanitizeTypes($type)) : $default;
    return $var;
}

function SanitizeAJAXPostVariables($varName, $type)
{
    return filter_input(INPUT_POST, $varName, SanitizeTypes($type));
}

function SanitizeOutput($value)
{
//    return htmlentities($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); 
    return htmlentities($value, ENT_QUOTES, 'UTF-8'); 
}

function SanitizeTypes($type)
{
    $filterType = FILTER_SANITIZE_STRING;
    switch($type)
    {
        // string
        case '1':
            $filterType = FILTER_SANITIZE_STRING;
            break;
        
        // integer
        case '2':
            $filterType = FILTER_SANITIZE_NUMBER_INT;
            break;
    }
    
    return $filterType;
}

function FieldValue($inputName, $defaultValue)
{
//    return isset($_POST[$inputName]) ? trim(SanitizeOutput($_POST[$inputName])) : trim(SanitizeOutput($defaultValue));
    return isset($_POST[$inputName]) ? trim($_POST[$inputName]) : trim($defaultValue);
}

function FieldValueArray($inputName, $defaultValue)
{
    return isset($_POST[$inputName]) ? trim($_POST[$inputName]) : $defaultValue;
}

//---------- Form Additional Info
// this is the function that creates the three vertical buttons at the
// right side of the form. Content needs to be created separately
function HTML_FormOptions($arr)
{
    $output = '<div class="card-header-actions float-right">';
    $output .= '<a class="btn btn-transparent p-1 m-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    $output .= '<i class="fas fa-ellipsis-v"></i>';
    $output .= '</a>';
    $output .= '<div class="dropdown-menu dropdown-menu-right">';

    // option list
    foreach($arr as $optionItem)
    {
        $output .= $optionItem;
    }

    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}

// this is the function that creates a custom button at the
// right side of a form
function HTML_FormCustomOptions()
{
    $output = '<div class="card-header-actions float-right">';

    $output .= HTML_Switch("isLocked", "", "<i class='fas fa-lock text-dark ml-1'></i>", false, false);

    $output .= '</div>';
    
    return $output;
}

//---------- Form Groups
// input and select controls come in different sizes
// we set the length/size of the control on a scale of 1 to 12
// where 12 is 100% of the control's container and 1 being the smallest
function HTML_FormGroup($name, $label, $formControl, $hasHelp, $helpText, $isRequired, $width = 12)
{
    $output = '<div class="form-group row">';
    $output .= '<div class="col-sm-'.$width.'">';
    $output .= HTML_FormLabel($name, $label, $hasHelp, $helpText, $isRequired);
    $output .= $formControl;
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}

function HTML_FormGroup_HelperText($name, $label, $formControl, $helpText, $width = 12)
{
    $output = '<div class="form-group row">';
    $output .= '<div class="col-sm-'.$width.'">';
    $output .= HTML_FormLabel($name, $label, false, "");
    $output .= $formControl;
    $output .= '<small id="'.$name.'Block" class="form-text text-muted" style="line-height:14pt">'.$helpText.'</small>';
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}

function HTML_FormLabel($name, $label, $hasHelp, $helpText, $isRequired = false)
{
    $output = "";
    $output .= '<label for="'.$name.'"';
    
    // if this is a required field
    if($isRequired)
    {
        $output .= " class='label-required'";
    }
    
    $output .= '>'.$label;
    $output .= '</label>';

    // add tooltip
    if($hasHelp)
    {
        $output .= '<a for="'.$name.'" tabindex="0" class="form-tooltip" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="'.$helpText.'">';
        $output .= '<i class="fas fa-question-circle ml-2"></i>';
        $output .= "</a>";
    }
    
    return $output;
}

//---------- Textboxes, Dropdowns and Textareas
// Textbox
/**
 * Creates a normal HTML textbox element
 * 
 * @param string $type The type of textbox (e.g. 'text','password','hidden' etc.)
 * @param string $name The name/id of the control
 * @param string $value The initial/default value to be assigned to the control
 * @param string $class The CSS style class to use
 * @param string $placeholder The placeholder value
 * @param boolean $isRequired Is this field required
 * @param boolean $isReadOnly Is this field read only
 * @param array $attributes Additional HTML attributes in an array
 * @return string
 * 
 */
function HTML_Input($type, $name, $value, $class, $placeholder, $isRequired, $isReadOnly, $attributes = null) {
    $output = '<input';
    $output .= ' id="' . $name . '"';
    $output .= ' type="' . $type . '"';
    $output .= ' name="' . $name . '"';
    $output .= ' value="' . $value . '"';
    $output .= ' class="form-control ' . $class . '"';
    $output .= ' placeholder="' . $placeholder . '"';

    if ($isReadOnly) {
        $output .= ' readonly';
    }
    if ($isRequired) {
        $output .= ' required';
    }

    // misc attributes
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= ">";
    return $output;
}

// Textarea
function HTML_Textarea($name, $value, $rows, $class, $placeholder, $isRequired, $isReadOnly, $attributes = null) {
    $output = '<textarea';
    $output .= ' id="' . $name . '" ';
    $output .= ' name="' . $name . '" ';
    $output .= ' rows="' . $rows . '" ';
    $output .= ' class="form-control ' . $class . '"';
    $output .= ' placeholder="' . $placeholder . '" ';

    if ($isReadOnly) {
        $output .= ' readonly';
    }
    if ($isRequired) {
        $output .= ' required';
    }

    // misc attributes
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= '>';
    $output .= $value;
    $output .= '</textarea>';

    return $output;
}

//----- HTML Dropdown (SELECT) :: start ----- //
/**
 * Creates a normal HTML select (dropdown) element
 * 
 * @param string $name The name/id of the control
 * @param string $value The initial/default value to be assigned to the control
 * @param string $class The CSS style class to use
 * @param boolean $isDisabled Is this field disabled
 * @param array $options the array containing the key:value data
 * @param array $attributes Additional HTML attributes in an array
 * @return string
 * 
 */
function HTML_Select($name, $value, $class, $isDisabled, $options, $attributes = null) {
    $output = '<select';
    $output .= ' id="' . $name . '"';
    $output .= ' name="' . $name . '"';
    $output .= ' class="form-control ' . $class . '"';

    if ($isDisabled) {
        $output .= ' disabled';
    }

    // misc attributes
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= ">";

    // options
    $output .= Bind_SelectOptions($value, $options);

    $output .= '</select>';
    return $output;
}

// select list options
function Bind_SelectOptions($selectedValue, $array) {
    $output = '';

    reset($array);
    foreach($array as $key => $val)
    {
        $output .= Generate_SelectOptions($selectedValue, $key, $val);
    }
    
    return $output;
}

// generate the individual options in the dropdown
function Generate_SelectOptions($selectedValue, $optionValue, $optionText = '') {
    $output = '';

    if ($optionText == '') {
        $optionText = $optionValue;
    }

    $output .= "<option value='$optionValue'";
    
    // for multiple select values, check whether $selectedValue is an array
    if(is_array($selectedValue))
    {
        if(array_key_exists($optionValue, $selectedValue))
        {
            $output .= " selected='selected'";
        }
    }
    else
    {
        if ($optionValue == $selectedValue) {
            $output .= " selected='selected'";
        }
    }

    $output .= ">$optionText</option>";

    return $output;
}

// create an array of IDs and Values for a dropdown
// from a data source containing more than the required
// key:value data
function SetDropdownOptionArray($sourceArr, $fieldID, $fieldName)
{
    $targetArr = array();
    foreach($sourceArr as $arr)
    {
        $id = $arr[$fieldID];
        $targetArr[$id] = $arr[$fieldName];
    }
    
    return $targetArr;
}
//----- HTML Dropdown (SELECT) :: end ----- //

// switch
function HTML_Switch($name, $value, $text, $isSelected, $isDisabled, $attributes = null)
{
    $output = '<span class="custom-control custom-switch pl-4 ml-3">';
    $output .= '<input type="checkbox" class="custom-control-input"';
    $output .= ' id="' . $name . '"';
    $output .= ' name="' . $name . '"';
    $output .= ' value="' . $value . '" ';

    if($isSelected || $isSelected == '1')
    {
        $output .= ' checked';
    }
    if($isDisabled)
    {
        $output .= ' disabled';
    }

    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= ">";

    $output .= '<label class="custom-control-label" for="'.$name.'" style="line-height:18pt;">'.$text.'</label>';
    $output .= '</span>';
    
    return $output;
}


// radio buttons
function HTML_RadioList_Inline($name, $arr)
{
    $output = "";
    foreach($arr as $radio)
    {
        $output .= HTML_Radio_Inline($radio['id'], $name, $radio['value'], $radio['text'], $radio['selected'], $radio['disabled'], $radio['required']);
    }
    
    return $output;
}

function HTML_Radio_Inline($id, $name, $value, $text, $isSelected, $isDisabled, $isRequired, $attributes = null)
{
    $output = '<div class="form-check form-check-inline mr-4">';
    $output .= '<input class="form-check-input" type="radio" ';
    $output .= ' id="' . $id . '"';
    $output .= ' name="' . $name . '"';
    $output .= ' value="' . $value . '"';
    
    if($isSelected || $isSelected == '1')
    {
        $output .= ' checked';
    }
    if($isDisabled)
    {
        $output .= ' disabled';
    }
    if($isRequired)
    {
        $output .= ' required';
    }
    
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }
    
    $output .= ">";
    
    $output .= '<label class="form-check-label" for="'.$name.'">';
    $output .= $text;
    $output .= '</label>';
    $output .= '</div>';
    
    return $output;
}

function HTML_Radio($id, $name, $value, $text, $isSelected, $isDisabled, $attributes = null)
{
    $output = '<div class="form-check">';
    $output .= '<input class="form-check-input" type="radio" ';
    $output .= ' id="' . $id . '"';
    $output .= ' name="' . $name . '"';
    $output .= ' value="' . $value . '"';
    
    if($isSelected || $isSelected == '1')
    {
        $output .= ' checked';
    }
    if($isDisabled)
    {
        $output .= ' disabled';
    }
    
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }
    
    $output .= ">";
    
    $output .= '<label class="form-check-label" for="'.$name.'">';
    $output .= $text;
    $output .= '</label>';
    $output .= '</div>';
    
    return $output;
}

//---------- Input Groups
function HTML_InputGroup_Prepend($type, $name, $value, $class, $placeholder, $isRequired, $isReadOnly, $inputGroupText, $attributes = null) {
    
    $output = '<div class="input-group">';
    
    $output .= '<div class="input-group-append">';
    $output .= '<span class="input-group-text">'.$inputGroupText.'</span>';
    $output .= "</div>";
    
    $output .= HTML_Input($type, $name, $value, $class, $placeholder, $isRequired, $isReadOnly, $attributes);
    $output .= "</div>";
    
    return $output;
}

function HTML_InputGroup_Append($type, $name, $value, $class, $placeholder, $isRequired, $isReadOnly, $inputGroupText, $attributes = null) {
    
    $output = '<div class="input-group">';
    $output .= HTML_Input($type, $name, $value, $class, $placeholder, $isRequired, $isReadOnly, $attributes);
    
    $output .= '<div class="input-group-append" for="'.$name.'">';
    $output .= '<span class="input-group-text">'.$inputGroupText.'</span>';
    $output .= "</div>";
    
    $output .= "</div>";
    
    return $output;
}

//---------- Media Objects
function HTML_MediaObject($media, $text, $align)
{
    $output = '<div class="media">';

    if(strtolower($align) == "left")
    {
        $output .= $media;
    }
    $output .= '<div class="media-body">';
    $output .= $text;
    $output .= '</div>';
    if(strtolower($align) == "right")
    {
        $output .= $media;
    }
    $output .= '</div>';

    return $output;
}

//---------- Other Form Elements
function HTML_Checkbox($name, $value, $isSelected, $isDisabled, $attributes = null) {
    
    $output = '<input type="checkbox"';
    $output .= ' id="' . $name . '" ';
    $output .= ' name="' . $name . '" ';
    $output .= ' value="' . $value . '" ';
    
    if($isSelected || $isSelected == '1')
    {
        $output .= ' checked';
    }
    if($isDisabled)
    {
        $output .= ' disabled';
    }
    
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= ">";

    return $output;
}

//---------- Form Buttons
// normal button (with postback)
function HTML_Button($name, $text, $buttonClass, $needsConfirm, $confirmText, $isDisabled, $isNoValidate, $attributes = null)
{
    $output = '<button ';
    $output .= 'type="submit" ';
    $output .= 'id="' . $name . '" ';
    $output .= 'name="' . $name . '" ';
    
    // class
    $output .= HTML_ButtonClass($buttonClass);
    
    // misc attributes
    if ($needsConfirm) {
        $output .= " onclick=\"return confirm('$confirmText')\"";
    }
    if ($isDisabled) {
        $output .= ' disabled';
    }
    if ($isNoValidate) {
        $output .= ' formnovalidate';
    }
    
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= ">";
    $output .= $text;
    $output .= "</button>";
    
    return $output;
}

// normal button (without postback)
function HTML_AjaxButton($name, $text, $buttonClass, $isDisabled, $attributes = null)
{
    $output = '<button ';
    $output .= 'type="button" ';
    $output .= 'id="' . $name . '" ';
    $output .= 'name="' . $name . '" ';
    
    // class
    $output .= HTML_ButtonClass($buttonClass." ".$name);
    
    // misc attributes
    if ($isDisabled) {
        $output .= ' disabled';
    }
    
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= ">";
    $output .= $text;
    $output .= "</button>";
    
    return $output;
}

// classless button
function HTML_ClasslessButton($name, $text, $needsConfirm, $confirmText, $isDisabled, $isNoValidate, $attributes = null)
{
    $output = '<button ';
    $output .= 'type="submit" ';
    $output .= 'id="' . $name . '" ';
    $output .= 'name="' . $name . '" ';
        
    // misc attributes
    if ($needsConfirm) {
        $output .= " onclick=\"return confirm('$confirmText')\"";
    }
    if ($isDisabled) {
        $output .= ' disabled';
    }
    if ($isNoValidate) {
        $output .= ' formnovalidate';
    }
    
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= ">";
    $output .= $text;
    $output .= "</button>";
    
    return $output;
}

// Submit Button
function HTML_Submit($name, $text, $buttonClass, $needsConfirm, $confirmText, $isDisabled, $isNoValidate, $attributes = null) {
    $output = '<input ';
    $output .= ' id="' . $name . '" ';
    $output .= ' type="submit" ';
    $output .= ' name="' . $name . '" ';
    $output .= ' value="' . $text . '" ';

    // class
    $output .= HTML_ButtonClass($buttonClass);

    // misc attributes
    if ($needsConfirm) {
        $output .= " onclick=\"return confirm('$confirmText')\"";
    }
    if ($isDisabled) {
        $output .= ' disabled';
    }
    if ($isNoValidate) {
        $output .= ' formnovalidate';
    }
    
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= ">";

    return $output;
}

// Link Button
function HTML_LinkButton($text, $href, $buttonClass, $attributes = null) {
    $output = "<a";

    // href
    if ($href != "") {
        $output .= " href='$href'";
    }

    // class
    $output .= HTML_ButtonClass($buttonClass);

    // misc attributes
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    // button text
    $output .= ">$text</a>";

    return $output;
}

// Normal Button (without submit feature)
function HTML_StatelessButton($name, $text, $buttonClass, $attributes = null)
{
    $output = '<input ';
    $output .= ' type="button" ';
    $output .= ' id="' . $name . '" ';
    $output .= ' name="' . $name . '" ';
    $output .= ' value="' . $text . '" ';
    
    // class
    $output .= HTML_ButtonClass($buttonClass);
    
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    $output .= ">";

    return $output;
}

//---------- Modals
function HTML_ModalButton($name, $text, $buttonClass, $attributes = null)
{
    $output = "<a ";
    $output .= 'href="#'.$name.'" ';
    $output .= 'data-toggle="modal" ';

    // class
    $output .= HTML_ButtonClass($buttonClass);

    // misc attributes
    if ($attributes != null) {
        $output .= HTML_Attributes($attributes);
    }

    // button text
    $output .= ">$text</a>";

    return $output;
}

function HTML_DismissModalButton()
{
    $output = '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
    return $output;
}

//---------- Common and Misc Functions
// Generic HTML control attributes
function HTML_Attributes($attributes) {
    $output = "";
    foreach ($attributes as $attr => $value) {
        if ($value != '') {
            $output .= " $attr='" . $value . "'";
        }
    }

    return $output;
}

// there are different button style classes in bootstrap
function HTML_ButtonClass($buttonClass) {
    $class = " class='btn btn-$buttonClass btn-bold'";
    return $class;
}

// simple bootstrap card
function HTML_Card($message, $bg = "light")
{
    $output = '<div class="card card-empty text-center">';
    $output .= $message;
    $output .= '</div>';
    
    return $output;
}

function HTML_Panel($message, $bgcolor = "light")
{
    $output = '<div class="card card-empty text-center bg-'.$bgcolor.' text-dark m-0 p-5 mb-1">';
    $output .= $message;
    $output .= '</div>';
    
    return $output;
}

// row spacer function
function HTML_RowSpace($height)
{
    $output = "<div style='display:block; clear:both; height: $height"."px; padding:0; margin:0'></div>";
    return $output;
}



// data status
function SetStatusText($status)
{
    return	$status	?	"Published"	:	"Not Published";
}

function SetStatusIcon($status)
{
    return	$status	?	"fa-check text-success"	:	"fa-times text-danger";
}

function SetStatusFont($status)
{
    return	$status	?	"&#xf00c;"	:	"&#xf00d;";
}

function SetOpenIcon($status)
{
    return	$status	?	"fas fa-check text-success"	:	"far fa-clock text-warning";
}

function Text_Status($status)
{
    return $status	?	"Active"	:	"Inactive";
}

// open icon
function Icon_Open($status)
{
    return '<i class="' . SetOpenIcon($status) . ' fa-fw fa-lg"></i>';
}

function Text_Open($status)
{
    return $status	?	"Completed"	:	"Pending";
}



// warning panel
function Panel_Warning($message, $showHelpdeskMessage = true)
{
    $output = '<div class="alert alert-danger" role="alert" style="margin: 10px; padding: 10px;">';
    $output .= $message;
    
    if($showHelpdeskMessage)
    {
        $output .= "<br>If the problem persists, "
                . 'please write in to our <strong><a href="#" class="text-danger">Helpdesk</a></strong> '
                . "with a description of the problem";
    }
    
    $output .= "</div>";
    
    return $output;
}

function HTML_RangeSlider($name, $text, $min, $max, $value)
{
    $output = '<div class="form-group p-0">';
    $output .= '<label for="'.$name.'">'.$text.'</label>';
    $output .= '<input type="range" class="custom-range" id="'.$name.'" name="'.$name.'" min="'.$min.'" max="'.$max.'" value="'.$value.'">';
    $output .= '</div>';

    return $output;
}

?>