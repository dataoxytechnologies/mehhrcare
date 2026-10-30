<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
</head>
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="">
                    <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div class="pprinta4">
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo $this->media_storage->getImageURL($print_details[0]['print_header']);
                            }
                            ?>" class="img-fluid sh-avatar-cover" >
                        </div>
                    <?php } ?>
                    <div class="divider mb-10 mt-10"></div>
                    <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <th width="15%"><?php echo $this->lang->line('name'); ?></th>
                            <td width="20%"><?php echo html_escape($result["patient_name"]); ?></td>
                            <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                            <td width="20%" class="text-start"><?php echo html_escape($result["age"])." Years, ".html_escape($result["month"])." Month"; ?></td>
                            <th width="10%"><?php echo $this->lang->line('gender'); ?></th>
                            <td width="20%" class="text-start"><?php echo html_escape($result["gender"]); ?></td>
                        </tr>
                         <tr>
                            <th width="20%"><?php echo $this->lang->line('admission_date') ; ?></th>
                            <td width="25%"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])); ?></td>
                            <th><?php echo $this->lang->line('discharged') . " " . $this->lang->line('date'); ?></th>
                            <td width="30%" class="text-start"><?php echo date($this->customlib->getHospitalDateFormat(true, false), strtotime($result['discharged_date'])); ?></td>
                        </tr>
                         <tr>
                            <th width="20%"><?php echo $this->lang->line('address') ; ?></th>
                            <td width="25%"><?php echo html_escape($result['address']); ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('diagnosis') ; ?></th>
                            <td width="25%"><?php echo html_escape($result["diagnosis"]); ?></td>
                        </tr>
                        <?php if (!empty($icd_codes)) { ?>
                        <tr>
                            <th width="20%" style="vertical-align:top"><?php echo $this->lang->line('icd10_diagnosis'); ?></th>
                            <td width="80%" colspan="5">
                                <?php foreach ($icd_codes as $icd) { ?>
                                <span style="display:inline-block; margin:1px 4px 1px 0; padding:1px 6px; background:#d6eaf8; border:1px solid #aed6f1; border-radius:3px; font-size:11px;">
                                    <strong><?php echo html_escape($icd['icd_code']); ?></strong> – <?php echo html_escape($icd['icd_description']); ?>
                                </span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr> <th><?php echo $this->lang->line('operation') ; ?></th>
                            <td width="30%" class="text-start"><?php echo html_escape($result["operation"]); ?></td>
                        </tr>
                          <tr>
                             <th><?php echo $this->lang->line('note') ; ?></th>
                            <td width="30%" class="text-start"><?php echo nl2br(html_escape($result['note'])); ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" id="testreport" width="100%">
                    <tr>
                        <th width="40%"><?php echo $this->lang->line('investigations'); ?></th> 
                        <th><?php echo $this->lang->line('treatment_at_home'); ?></th>
                    </tr>
                    <tr>
                        <td width="40%"><?php echo nl2br(html_escape($result["investigations"])); ?></td>
                        <td><?php echo nl2br(html_escape($result["treatment_home"])); ?></td>
                    </tr>
                    </table> 
                    <div class="divider mb-10 mt-10"></div>  
                    <div class="footer-fixed printfooter"> 
                        <p><?php
                            if (!empty($print_details[0]['print_footer'])) {
                                echo $print_details[0]['print_footer'];
                            }
                            ?></p>
                    </div>        
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </div>
</html>
<script type="text/javascript">
    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pharmacy/deletePharmacyBill/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }
    function printData(id) {

        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/pharmacy/getBillDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {               
                popup(result);
            }
        });
    }    
</script>