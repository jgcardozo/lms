<?php
if (!isset($field['entity'])) {
    $field['entity'] = array_first(explode('.', $field['name']));
}
if (!isset($field['attribute'])) {
    $field['attribute'] = array_last(explode('.', $field['name']));
}

if(!empty($entry) && $entry->{$field['entity']})
{
    $field['value'] = $entry->{$field['entity']}->{$field['attribute']};
}
?>@include('crud::fields.text')