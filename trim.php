<?php

function trim_array(&$array) {
    array_walk($array, function(&$value) {
        if (is_array($value)) {
            trim_array($value);
        } else {
            $value = trim($value);
        }
    });
  }