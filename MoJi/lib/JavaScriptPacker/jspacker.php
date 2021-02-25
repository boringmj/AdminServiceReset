<?php

/** 来自 wuliaomj 的吐槽
 * 这玩意我整理了下
 * 无论是混淆还是加密,我觉得程度都不大
 * 把 eval 改成 alert 就解开了
 * 不过可以用来压缩js
 */

require __DIR__.'/class.JavaScriptPacker.php';

function javascript_encode($script)
{
	$packer = new JavaScriptPacker($script, 'Normal', true, false);
	$packed = $packer->pack();
	return $packed;
}

?>
