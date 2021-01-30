<?php
	namespace Connection;

	function removeNumericIndexesOfArray(array &$array) {
		foreach ($array as $i => $value) {
			if (is_numeric($i)) {
				unset($array[$i]);
			}
		}
	}
?>