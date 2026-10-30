
<div class="sh-cert-preview<?php echo empty($certificate->background_image) ? ' sh-cert-empty' : ''; ?>">
    <?php if (!empty($certificate->background_image)) { ?>
        <img class="sh-cert-bg" src="<?php echo $this->media_storage->getImageURL('uploads/certificate/' . $certificate->background_image); ?>" alt="" />
    <?php } ?>

    <table width="100%" cellspacing="0" cellpadding="0" style="position: absolute;top: 0; text-align: left; margin-left: auto; margin-right: auto;left: 0;right: 0;<?php echo "width:" . (int)$certificate->content_width . "px" ?>">
        <tr>
            <td class="sh-cert-photo-td">
                <?php if ($certificate->enable_patient_image == 1) { ?>
                    <img style="position: relative; <?php echo "top:" . (int)$certificate->enable_image_height . "px" ?>;" src="<?php echo $this->media_storage->getImageURL('uploads/patient_images/no_image.png') ?>" width="100" height="auto">
                <?php } ?>
            </td></tr>
        <tr>
            <td valign="top" style="text-align:left; position: relative; <?php echo "top:" . (int)$certificate->header_height . "px" ?>"><?php echo $certificate->left_header ?></td>
            <td valign="top" style="text-align:center; position: relative; <?php echo "top:" . (int)$certificate->header_height . "px" ?>"><?php echo $certificate->center_header ?></td>
            <td valign="top" style="text-align:right; position: relative; <?php echo "top:" . (int)$certificate->header_height . "px" ?>"><?php echo $certificate->right_header ?></td>
        </tr>
        <tr>
            <td colspan="3" valign="top" style="position: relative;<?php echo "top:" . (int)$certificate->content_height . "px" ?>">
                <p class="sh-cert-body-p"><?php echo $certificate->certificate_text ?></p></td>
        </tr>
        <tr>
            <td valign="top" style="text-align:left;position: relative; <?php echo "top:" . (int)$certificate->footer_height . "px" ?>"><?php echo $certificate->left_footer ?></td>
            <td valign="top" style="text-align:center;position: relative; <?php echo "top:" . (int)$certificate->footer_height . "px" ?>"><?php echo $certificate->center_footer ?></td>
            <td valign="top" style="text-align:right;position: relative; <?php echo "top:" . (int)$certificate->footer_height . "px" ?>"><?php echo $certificate->right_footer ?></td>
        </tr>
    </table>
</div>
