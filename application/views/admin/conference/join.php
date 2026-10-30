<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $this->customlib->getAppName(); ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta http-equiv="Cache-control" content="no-cache">
        <meta name="theme-color" content="#424242" />
        <meta name="format-detection" content="telephone=no">
        
		<?php 
            $logoresult = $this->customlib->getLogoImage();
            if (!empty($logoresult['mini_logo'])) {
                $mini_logo = base_url() . 'uploads/hospital_content/logo/' . $logoresult['mini_logo']; 
            }else{
                $mini_logo = base_url() . 'backend/images/s-favican.png';
            }
         ?>
		
		<link href="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/<?php echo $mini_logo; ?>" rel="shortcut icon" type="image/x-icon">
		<link type="text/css" rel="stylesheet" href="https://source.zoom.us/3.11.2/css/bootstrap.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-theme.css">
	
    </head>

    <body oncontextmenu="return false;" class="sh-conference-join">

<nav id="nav-tool" class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <h4><i class="fab fa-chromecast"></i> <?php echo $this->lang->line('live_consultation_title'); ?>: <?php echo html_escape($title); ?></h4>
                </div>
                <div class="navbar-form navbar-right">
                    <h5><i class="far fa-user-circle"></i> <?php echo $this->lang->line('host_by'); ?>: <?php echo html_escape($host); ?></h5>
                </div>
            </div>
        </nav>
		
    <script src="https://source.zoom.us/3.11.2/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/3.11.2/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/3.11.2/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/3.11.2/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/3.11.2/lib/vendor/lodash.min.js"></script>
    <script src="https://source.zoom.us/3.11.2/zoom-meeting-3.11.2.min.js"></script>
    
    <script type="text/javascript">
   

        window.addEventListener('DOMContentLoaded', function(event) {
			console.log('DOM fully loaded and parsed');
			websdkready();
		});

	function websdkready() {
 
		ZoomMtg.setZoomJSLib("https://source.zoom.us/3.11.2/lib", "/av"); // CDN version default

		ZoomMtg.preLoadWasm();
		ZoomMtg.prepareWebSDK();


		var meetingConfig = {
			sdkKey: "<?php echo $zoom_api_key; ?>",
			meetingNumber: "<?php echo $meetingID; ?>",
			userName: <?php echo json_encode($name); ?>,
			passWord: <?php echo json_encode($meeting_password); ?>,
			leaveUrl: "<?php echo site_url($leaveUrl); ?>",
			role: 1,
			userEmail: "",
			lang:"en-US",
			china: false,
			sdkSecret: "<?php echo $zoom_api_secret; ?>",
			webEndpoint:'zoom.us'
		};
		console.log(meetingConfig);
    
		var signature = ZoomMtg.generateSDKSignature({
			meetingNumber: meetingConfig.meetingNumber,
			sdkKey: meetingConfig.sdkKey,
			sdkSecret: meetingConfig.sdkSecret,
			role: meetingConfig.role,
			success: function(res) {
				console.log(res.result);
			},
		});    
		
		function beginJoin(signature) {
     
			ZoomMtg.init({
           
				leaveUrl: meetingConfig.leaveUrl,
				webEndpoint: meetingConfig.webEndpoint,
				disableCORP: !window.crossOriginIsolated, // default true            
				externalLinkPage: './externalLinkPage.html',
				success: function() {              
					ZoomMtg.i18n.load(meetingConfig.lang);
					ZoomMtg.i18n.reload(meetingConfig.lang);
					ZoomMtg.join({
						meetingNumber: meetingConfig.meetingNumber,
						userName: meetingConfig.userName,
						signature: signature,
						sdkKey: meetingConfig.sdkKey,
						userEmail: meetingConfig.userEmail,
						passWord: meetingConfig.passWord,
						success: function(res) {
							console.log("join meeting success");
							console.log("get attendeelist");
							ZoomMtg.getAttendeeslist({});
							ZoomMtg.getCurrentUser({
								success: function(res) {
									console.log("success getCurrentUser", res.result.currentUser);
								},
							});
						},
						error: function(res) {
							console.log(res);
						},
					});
				},
				error: function(res) {
					console.log(res);
				},
			});
    
			ZoomMtg.inMeetingServiceListener('onUserJoin', function(data) {
				console.log('inMeetingServiceListener onUserJoin', data);
			});
		
			ZoomMtg.inMeetingServiceListener('onUserLeave', function(data) {
				console.log('inMeetingServiceListener onUserLeave', data);
			});
		
			ZoomMtg.inMeetingServiceListener('onUserIsInWaitingRoom', function(data) {
				console.log('inMeetingServiceListener onUserIsInWaitingRoom', data);
			});
		
			ZoomMtg.inMeetingServiceListener('onMeetingStatus', function(data) {
				console.log('inMeetingServiceListener onMeetingStatus', data);
			});
		}

		beginJoin(signature);

	}

    </script>
</body>
</html>
