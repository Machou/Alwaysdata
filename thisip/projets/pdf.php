<?php
require_once '../../config/config.php';

// https://mpdf.github.io/

$titrePdf = 'Mon document PDF';

$mpdf = new \Mpdf\Mpdf([
	'mode' => 'utf-8',
	'format' => 'A4',
	// 'orientation' => 'L',
	'margin_left' => 3,
	'margin_right' => 3,
	'margin_top' => 3,
	'margin_bottom' => 3,
	'margin_header' => 3,
	'margin_footer' => 3,
]);

$mpdf->SetHTMLHeader('<div style="text-align: right; font-weight: bold;">My document</div>');

$mpdf->SetHTMLFooter('<table width="100%">
	<tr>
		<td width="33%">{DATE j-m-Y}</td>
		<td width="33%" align="center">{PAGENO}/{nbpg}</td>
		<td width="33%" style="text-align: right;">My document</td>
	</tr>
</table>');

$mpdf->WriteHTML('<h2>Hello world!</h2>

<p>Salut !</p>

<p style="color: rgba(255,0,0, 1);">Ca va ?</p>

<p>Oui et toi <strong>toi</strong> !</p>

<style>
.barcode {
	padding: 1.5mm;
	margin: 0;
	vertical-align: top;
	color: rgba(0,0,68, 1);
}

.barcodecell {
	text-align: center;
	vertical-align: middle;
}
</style>

<div class="barcodecell"><barcode code="54321068" type="I25" class="barcode" /></div>');

$mpdf->Output();