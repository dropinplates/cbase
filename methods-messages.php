<?php
/**
* @author	Amatz Fox - ZERO32
* @since	September 21, 2018
* @type		Message Page
*/

//print_r($thisMessage);
//echo json_encode($thisMessage, JSON_FORCE_OBJECT)

$getMessageNew = $getMessageMeta->listings(['recipient'=>$_SESSION["userid"],'join'=>'cp_messages on parent_id = message_id or cp_messages.user = '.$_SESSION["userid"],'statement'=>'group by parent_id order by date desc'],['cp_messages_meta.id as id','parent_id as parent_id','recipient as recipient','message_id as message_id','subject as subject','cp_messages_meta.status as status','cp_messages_meta.date as date','cp_messages_meta.recipient as recipient']);
$getMessage = $getMessageMeta->listings(['recipient'=>$_SESSION["userid"],'status<'=>1,'statement'=>'order by id desc'],getTableFields('messages_meta',''));
$getSentMessage = $getMessageMeta->listings(['owner'=>$_SESSION["userid"],'status<'=>1,'statement'=>'order by id desc'],getTableFields('messages_meta',''));
/*
echo '<code>';
var_dump($getMessage);
echo '</code>';
*/
?>

<div class="x_panel">
    <div class="x_title">
    <h2>Inbox Design<small>User Mail</small></h2>
    <ul class="nav navbar-right panel_toolbox">
        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </li>
        <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
        <ul class="dropdown-menu" role="menu">
            <li><a href="#">Settings 1</a>
            </li>
            <li><a href="#">Settings 2</a>
            </li>
        </ul>
        </li>
        <li><a class="close-link"><i class="fa fa-close"></i></a>
        </li>
    </ul>
    <div class="clearfix"></div>
    </div>
    <div class="x_content">
    <div class="row">
        <div class="col-sm-3 mail_list_column">
        <button id="compose" class="btn btn-sm btn-success btn-block" type="button">COMPOSE</button>
        <?php
		//print_r($getMessage);
		$thisParentID = 0;
        foreach($getMessageNew as $msgDetail){ // MESSAGE_META
            $thisMessage = $getMessages->listings(['id'=>$msgDetail['message_id']],getTableFields('messages',['content','status']));
            if($thisParentID != $thisMessage[0]['parent_id']){

                $thisSender = $getUsers->listings(['id'=>$thisMessage[0]['user']],['firstname','lastname']);
            ?>
            <button parentID="<?php echo $thisMessage[0]['parent_id']?>" name="messages_<?php echo $msgDetail['id'];?>" area="inbox-body" href="#" id="<?php echo $msgDetail['id'];//$thisMessage[0]['parent_id']?>" class="mail_list inboxBtn<?php echo ' msgStatus'.$msgDetail['status'];?>">

                <i class="fa fa-envelope<?php ($msgDetail['status'] < 1)? print '': print '-o' ?>"></i>

            <div class="right">
                <h3><?php echo $thisSender[0]['firstname'].' '.$thisSender[0]['lastname']?> <small><?php echo timeDateFormat($msgDetail['date'],'dateTime2')?></small></h3>
                <p class="ellipsis"><?php echo $thisMessage[0]['subject']?></p>
            </div>
            </button>

			<?php
                } // END IF
                $thisParentID = $thisMessage[0]['parent_id'];
			} ?>
        </div>
        <!-- /MAIL LIST -->

        <!-- CONTENT MAIL -->
        <div class="col-sm-9 mail_view">
            <div class="inbox-body"><span class="glyphicon glyphicon-envelope huge" aria-hidden="true"></span><p class="alignCenter">To view conversation, click on it.</p></div>

            <div class="btn-group">
            <button id="" recipient="" name="replyBtn" class="btn btn-sm btn-primary reply" type="button"><i class="fa fa-reply"></i> Reply</button>
            <button class="btn btn-sm btn-default" type="button"  data-placement="top" data-toggle="tooltip" data-original-title="Forward"><i class="fa fa-share"></i></button>
            <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Print"><i class="fa fa-print"></i></button>
            <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Trash"><i class="fa fa-trash-o"></i></button>
            </div>
        </div>
        <!-- /CONTENT MAIL -->
    </div>
    </div>
</div>
</div>

<div class="compose col-md-7 col-xs-12">
      <div class="compose-header">
        <span>New Message</span>
        <button type="button" class="close compose-close">
          <span>×</span>
        </button>
      </div>
<form id="createmessage" data-toggle="validator" name="<?php echo $theID?>" class="form-label-left input_mask" novalidate>
    <input type="hidden" name="action" id="action" value="createmessage" />
    <input type="hidden" name="data" id="data" value="create" />
    <input type="hidden" name="theID" id="theID" value="0" />
    <input type="hidden" name="parent_id" id="parent_id" value="0" />
    <input type="hidden" name="table" id="table" value="messages" />
    <input type="hidden" name="status" id="status" value="0" />
    <input type="hidden" name="type" id="msg_type" value="1" />
    <input type="hidden" name="attachment" id="attachment" value="" />
      <div class="compose-body">
        <div id="alerts"></div>
		<?php
		$getFieldGroup = new fieldGroup;
		$recipient = $getFieldGroup->inputGroup(['label'=>'Recipient:','type'=>'select2','id'=>'recipient','name'=>'recipient','meta_key'=>'recipient','meta'=>'users','title'=>'recipients','required'=>'required','placeholder'=>'Recipient Name','value'=>'']);
		//$recipientCC = $getFieldGroup->inputGroup(['label'=>'CC:','type'=>'text','id'=>'recipient_cc','name'=>'recipient_cc','placeholder'=>'CC','value'=>'']);
		$subject = $getFieldGroup->inputGroup(['label'=>'Subject:','type'=>'text','id'=>'subject','name'=>'subject','placeholder'=>'Message Title','value'=>'']);
		?>
		<div class="form-group no-padding item" id="messageRecipients">
			<?php echo $recipient?>
		</div>
		<div class="form-group no-padding item" id="messageSubject">
			<?php echo $subject?>
		</div>


        <div id="editor" class="editor-wrapper"></div>
      </div>

      <div class="compose-footer">
		<div class="btn-toolbar editor" data-role="editor-toolbar" data-target="#editor">
			<div class="btn-group">
				<button id="send" name="createMessage" class="btn btn-sm btn-success" type="button" onclick="createOption('createmessage')"><i class="fa fa-send"></i>Send</button>
			</div>
          <div class="btn-group">
            <a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
            <a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>
            <a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
            <a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
          </div>
          <div class="btn-group">
            <a class="btn" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>
            <a class="btn" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>
          </div>
          <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="fa fa-link"></i></a>
            <div class="dropdown-menu input-append">
              <input class="span2" placeholder="URL" type="text" data-edit="createLink" />
              <button class="btn" type="button">Add</button>
            </div>
            <a class="btn" data-edit="unlink" title="Remove Hyperlink"><i class="fa fa-cut"></i></a>
          </div>
          <div class="btn-group">
            <a class="btn" title="Insert picture (or just drag & drop)" id="pictureBtn"><i class="fa fa-picture-o"></i></a>
            <input type="file" data-role="magic-overlay" data-target="#pictureBtn" data-edit="insertImage" />
          </div>

          <div class="btn-group">
            <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>
            <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>
          </div>
        </div>
      </div>
      </form>
    </div>
    <!-- /compose -->


<script>
$(document).ready(function() {
	function initToolbarBootstrapBindings() {
	  var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
		  'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
		  'Times New Roman', 'Verdana'
		],
		fontTarget = $('[title=Font]').siblings('.dropdown-menu');
	  $.each(fonts, function(idx, fontName) {
		fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\'' + fontName + '\'">' + fontName + '</a></li>'));
	  });
	  $('a[title]').tooltip({
		container: 'body'
	  });
	  $('.dropdown-menu input').click(function() {
		  return false;
		})
		.change(function() {
		  $(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');
		})
		.keydown('esc', function() {
		  this.value = '';
		  $(this).change();
		});

	  $('[data-role=magic-overlay]').each(function() {
		var overlay = $(this),
		  target = $(overlay.data('target'));
		overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
	  });

	  if ("onwebkitspeechchange" in document.createElement("input")) {
		var editorOffset = $('#editor').offset();

		$('.voiceBtn').css('position', 'absolute').offset({
		  top: editorOffset.top,
		  left: editorOffset.left + $('#editor').innerWidth() - 35
		});
	  } else {
		$('.voiceBtn').hide();
	  }
	}

	function showErrorAlert(reason, detail) {
	  var msg = '';
	  if (reason === 'unsupported-file-type') {
		msg = "Unsupported format " + detail;
	  } else {
		console.log("error uploading file", reason, detail);
	  }
	  $('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>' +
		'<strong>File upload error</strong> ' + msg + ' </div>').prependTo('#alerts');
	}

	initToolbarBootstrapBindings();

	$('#editor').wysiwyg({
	  fileUploadError: showErrorAlert
	});

	prettyPrint();
  });

$("button.inboxBtn").on('click',function () {
	theID = $(this).attr('id');
	theArea = $(this).attr('area');
	theParentID = $(this).attr('parentID');
	action = 'getMessageBox';
	formData = {'action':action,'id':theID,'sessionUserID':<?php echo $_SESSION["userid"]?>};
	jQuery.ajax({
	url: "storage.php",
	data:formData,
	type: "POST",
	success:function(data){
		//alert(data);
		var status = $('.compose').is(":hidden");
        if(!status){
            $('.compose').slideToggle();
        }
		clearMsgBox();
		$('.'+theArea).html(data.msgBody);
		$('button[name=replyBtn]').attr('id',theID).attr('recipient',data.recipient).attr('parentID',theParentID);
		$('button[name=messages_'+theID+']').removeClass('msgStatus0').addClass('msgStatus1');
		$('button[name=messages_'+theID+'] > i').removeClass('fa-envelope').addClass('fa-envelope-o');

		//alert(dataID+" | "+data.staffID+" | "+data.type+" | "+data.dateIn+" | "+data.dateOut);
	},
	error:function (){}
	});
});

$('#compose, .compose-close, .reply').click(function(){
    var status=$('.compose').is(":hidden");
//alert(status);
    $('.compose').slideToggle();
	btnName = $(this).attr('name');
	btnParentID = $(this).attr('parentID');



	$('ul.select2-selection__rendered').html('<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" placeholder="Recipient Name"></li>');

	if(btnName == 'replyBtn'){
		btnRecipient = $(this).attr('recipient');
		msgID = $(this).attr('id');
		msgHeader = $('h4#msgHeader').text();
		$('.compose-header > span:first-of-type').html('Reply: <strong>'+msgHeader+'</strong>');
		$('form#createmessage input[name=theID]').val(msgID);
		$('form#createmessage input[name=parent_id]').val(btnParentID);
		$('form#createmessage select[name=recipient] option[value='+btnRecipient+']').attr('selected','selected');
		recipientName = $('form#createmessage select[name=recipient] option[value='+btnRecipient+']').text();
		$('ul.select2-selection__rendered').prepend('<li class="select2-selection__choice" title="Kian"><span class="select2-selection__choice__remove" role="presentation">×</span>'+recipientName+'</li>');
		$('form#createmessage button[name=createMessage]').attr('id','reply').html('<i class="fa fa-reply"></i>Reply');
		$('form#createmessage input[name=subject]').val(msgHeader).attr('readonly',true);
		$('form#createmessage #messageSubject').hide();
	}else{
        clearMsgBox();
	}
});

function clearMsgBox(){
    $('.compose-header > span:first-of-type').html('New Message');
    $('form#createmessage #messageSubject').show();
    $('form#createmessage input[name=theID], form#createmessage input[name=parent_id]').val(0);
    $('form#createmessage input[name=subject]').val('').attr('readonly',false);
    $('form#createmessage select[name=recipient]').find('option:selected').attr('selected',false);
    $('form#createmessage button[name=createMessage]').attr('id','send').html('<i class="fa fa-send"></i>Send');
}

$(".select2_multiple").select2({
    maximumSelectionLength: 4,
    placeholder: "With Max Selection limit 4",
    allowClear: true
});
</script>
