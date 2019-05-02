<div class="wrap">
<h2>Webfonts</h2>
<form method="post" action="options.php">
    
    <?php if(isset( $_GET['settings-updated'])) { ?>
    <div class="updated">
        <p>Webfonts updated.</p>
    </div>
    <?php } ?>
    <div class="lay-explanation add-webfont-explanation <?php echo get_option('expand-how_to_add_webfont', 'expanded'); ?>" data-expand-status-option-name="expand-how_to_add_webfont">
        <header>
            <h3 class="title">How to add a Webfont</h3>
            <button class="lay-explanation-handle"></button>
        </header>
        <div class="lay-explanation-inner">
            <h2>Add by font file</h2>
            <h3 class="title">Convert your font to .woff</h3>
            <p>
                The <code>.woff</code> font-format is the most widely supported Webfont format.
                <br>Make sure you are using a free webfont or have bought a webfont-license for this font!
            </p>
            <p>
                Please convert your <code>.ttf</code> font into a <code>.woff</code> font here: <a href="http://everythingfonts.com/ttf-to-woff" target="_blank">everythingfonts.com/ttf-to-woff</a><br>
                Or convert your <code>.otf</code> font into a <code>.woff</code> font here: <a href="http://everythingfonts.com/otf-to-woff" target="_blank">everythingfonts.com/otf-to-woff</a>
            </p>
            <h3 class="title">Upload your .woff</h3>
            <p>
                Click on <span class="lay-label label-info">+ Add Font</span> below and then on <span class="lay-label label-info">Add by ".woff" file</span> to upload your file.
            </p>

            <h2>Add by "&lt;link&gt;" tag and CSS</h2>
            <p>
                Click on <span class="lay-label label-info">+ Add Font</span> below and then on <span class="lay-label label-info">Add by "&lt;link&gt;" tag and CSS</span>. Enter a font name, the "&lt;link&gt;" tag and your font-family CSS rule.
            </p>

            <h2>Add by "&lt;script&gt;" tag and CSS</h2>
            <p>
                Click on <span class="lay-label label-info">+ Add Font</span> below and then on <span class="lay-label label-info">Add by "&lt;script&gt;" tag and CSS</span>. Enter a font name, the JavaScript code and your font-family CSS rule.<br>
                Please note that Webfonts added by "&lt;script&gt;" tag and CSS are not displayed correctly in the text editor. Also, here in the Webfonts Manager they will only be displayed correctly after you clicked <span class="lay-label label-info">Save Changes</span>.
                <!-- Remember to click <span class="lay-label label-info">Save Changes</span> when you are done. -->
            </p>

            <h2 class="title">Usage</h2>
            <p>
                After you added your Webfont it is ready to use. It will appear in the "Font Family" dropdown menus in <a href="<?php echo admin_url( 'admin.php?page=laygridder-textformats' ); ?>">Text Formats</a>, in the Customizer and when you edit texts in "Projects" and "Pages".
            </p>
        </div>
    </div>

    <div id="fontmanager"></div>

    <?php settings_fields( 'laygridder-webfonts' ); ?>
    <?php do_settings_sections( 'laygridder-webfonts' ); ?>
    
    <?php submit_button(); ?>
</form>
</div>
<?php   
// on an older php version i saw that underscore js templates get interpreted as php 
// when in reality its just html outside the php wrap. having it inside the php wrap and echoing it works
echo
'<script type="text/template" id="row-composite-view">
    <div class="alert alert-danger wrong-file-notice" role="alert"><strong>Not a .woff font.</strong> Can\'t use this file because it\'s not a .woff font.</div>
    <div class="panel panel-default fontmanager-panel">
      <div class="panel-heading">
        <h3 class="panel-title">Font Manager</h3>
      </div>
      <table class="table font-table table-bordered">
        <thead>
          <tr>
            <th>Font Name</th>
            <th>Test Sentence</th>
            <th>Type</th>
            <th></th>
          </tr>
        </thead>
        <tbody class="rows">
        </tbody>
      </table>
      <div class="panel-footer clearfix">
        <button type="button" class="btn btn-info js-add-font btn-sm pull-right"><span class="glyphicon glyphicon-plus"></span> Add Font</button>
      </div>
    </div>

    <!-- modal -->
    <div id="add-webfont-input-modal" class="lay-input-modal">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Add Webfont</h3>
                <h3 class="panel-title panel-title-editmode">Edit Webfont</h3>
                <button type="button" class="close close-modal-btn"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            </div>
            <div class="panel-body">

                <div class="alert alert-warning edit-mode-warning" role="alert"><strong>About Editing:</strong> Is this font currently in use on your website? When you edit this font and save the changes, you will need to re-apply this font wherever you have been using it to see the changes. If you need to change the font of Texts, it\'s easier to add a new Webfont and use Textformats.</div>

                <div class="webfont-type-choice-wrap">   
                    <span class="woff-tooltip" data-toggle="tooltip" data-placement="top" title="Make sure your .woff filename does not start with a number and does not contain spaces or special signs.">                 
                        <button type="button" class="btn btn-info js-upload-woff-btn">Add by ".woff" file</button>
                    </span>
                    <button type="button" class="btn btn-info js-link-css-btn">Add by "&lt;link&gt;" tag and CSS</button>
                    <button type="button" class="btn btn-info js-js-css-btn">Add by "&lt;script&gt;" tag and CSS</button>
                </div>

                <form class="js-css-modal lay-webfont-form">
                    <div class="alert alert-warning" role="alert">
                        Please note that Webfonts added by "&lt;script&gt;" tag and CSS are not displayed correctly in the text editor. Also, here in the Webfonts Manager they will only be displayed correctly after you clicked <span class="lay-label label-info">Save Changes</span>.
                    </div>
                    <p>Please insert your "Font name", "JavaScript Embed Code" and your "font-family" CSS below. Don\'t insert any other CSS styles like "font-weight" or "font-style"!</p>                    
                    <p>Font name:</p>
                    <input type="text" class="form-control js-js-font-name-input" data-parsley-nonumberasfirstchar data-parsley-type="alphanum" data-parsley-errors-messages-disabled placeholder="FiraSans2">
                    <p>JavaScript Code:</p>
                    <input type="text" class="form-control js-js-embed-code" data-parsley-scripttag data-parsley-errors-messages-disabled placeholder="&lt;script src=\'//use.typekit.net/xxxxxxx.js\'&gt;&lt;/script&gt;&lt;script&gt;try{Typekit.load();}catch(e){}&lt;/script&gt;">
                    <p>"font-family" CSS:</p>
                    <input type="text" class="form-control js-js-font-family-input" data-parsley-fontfamily data-parsley-errors-messages-disabled placeholder="font-family: \'fira-sans-2\',sans-serif;">
                </form>

                <form class="link-css-modal lay-webfont-form">
                    <p>Please insert your "Font name", "Link tag" and "font-family" CSS below.<br>Don\'t insert any other CSS styles like "font-weight" or "font-style"!</p>
                    <p>Font name:</p>
                    <input type="text" class="form-control js-link-font-name-input" data-parsley-nonumberasfirstchar data-parsley-type="alphanum" data-parsley-errors-messages-disabled placeholder="Montserrat">
                    <p>Link tag:</p>
                    <input type="text" class="form-control js-link-tag-input" data-parsley-linktag data-parsley-errors-messages-disabled placeholder="<link href=\'http://fonts.googleapis.com/css?family=Montserrat:400,700\' rel=\'stylesheet\' type=\'text/css\'>">
                    <p>"font-family" CSS:</p>
                    <input type="text" class="form-control js-link-font-family-input" data-parsley-fontfamily data-parsley-errors-messages-disabled placeholder="font-family: \'Montserrat\', sans-serif;">
                </form>

            </div>
            <div class="panel-footer clearfix">
                <button type="button" class="btn btn-default btn-lg js-ok-button js-add-link-font-btn" disabled="disabled" style="display:none;">Ok</button>
                <button type="button" class="btn btn-default btn-lg js-ok-button js-add-js-font-btn" disabled="disabled" style="display:none;">Ok</button>
            </div>
        </div>
        <div class="background"></div>
    </div>
</script>

<script type="text/template" id="row-view">
    <td class="fontname" <%= showFontCSS() %>><%= showFontName() %></td>
    <td class="font-preview" <%= showFontCSS() %>><%= showText() %></td>
    <td class="font-type"><%= showType() %></td>
    <td><button data-type="<%= showType() %>" type="button" class="btn btn-default btn-sm js-edit-row btn-block"><span class="glyphicon glyphicon-pencil"></span> Edit</button>
    <button type="button" class="btn btn-default btn-sm js-delete-row btn-block"><span class="glyphicon glyphicon-remove"></span> Remove</button></td>
</script>';