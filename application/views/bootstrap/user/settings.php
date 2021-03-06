<?php
if (!empty($valid) && $valid):
?>
<div class="page-header"><h1>Edit User:  <?php echo $user['username']; ?></h1></div>
<small>
    <a href="<?php echo Url::format('/user/link'); ?>">Manage IRC Credentials</a>&nbsp;-&nbsp;
    <a href="<?php echo Url::format('/user/logs'); ?>">View Account Activity</a>&nbsp;-&nbsp;
    <a href="<?php echo Url::format('/user/connections'); ?>">Manage Your Connections</a>
</small>
<form class="well form-horizontal" action="<?php echo Url::format('/user/settings/save'); ?>" method="post">
<fieldset>
	<legend>Account Information</legend>
<?php if (CheckAcl::can('changeUsername')): ?>
	<div class="control-group">
		<label class="control-label">Username:</label>
		
		<div class="controls">
			<input type="text" name="username" value="<?php echo htmlentities($user['username'], ENT_QUOTES, '', false); ?>" />
		</div>
	</div>
<?php endif; ?>
	
	<div class="control-group">
		<label class="control-label">Email:</label>
		
		<div class="controls">
			<input type="text" name="email" value="<?php echo htmlentities($user['email'], ENT_QUOTES, '', false); ?>"/>
		</div>
	</div></fieldset>
	
	<div class="control-group">
		<label class="control-label">Hide your email?</label>
		
		<div class="controls">
			<input type="checkbox" name="hideEmail" value="true"<?php echo ($user['hideEmail'] ? ' checked="checked"' : ''); ?> />
		</div>
	</div>
    
    <div class="control-group">
        <label class="control-label">Lock sessions?</label>
        
        <div class="controls">
            <input type="checkbox" name="lockToIp" value="true"<?php echo ($user['lockToIP'] ? ' checked="checked"' : ''); ?> />
        </div>
    </div>
</fieldset>
<fieldset>    
	<legend>Change Password</legend>
	
	<div class="control-group">
		<label class="control-label">Old Password:</label>
		
		<div class="controls">
			<input type="text" name="oldpassword" />
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label">New Password:</label>
		
		<div class="controls">
			<input type="text" name="password" />
		</div>
	</div>
	
    <input type="submit" name="submit" value="Save" class="btn btn-primary" />
</fieldset>
</form>

<legend>Add a Certificate</legend>
<form class="well forum-vertical" action="<?php echo Url::format('user/certificate/add'); ?>" method="post">
	<textarea style="width: 100%" rows="12" name="csr" placeholder="The contents of your CSR"></textarea><br />
	<input type="submit" value="Submit" class="btn btn-primary" />
</form>
<br />

<legend>Certificates</legend>

<?php if (!empty($user['certs'])): 
?>
<table class="table table-striped">
	<thead>
        <td><i class="icon-question-sign" title="In Use?"></i></td>
		<td>#</td>
		<th>Hash</th>
		<th>Organization</th>
		<th>Valid From</th>
		<th>Valid To</th>
        <td style="width: 1%;"></td>
        <td style="width: 1%;"></td>
	</thead>
	<tbody>
<?php foreach ($user['certs'] as $cert): ?>
	<tr>
        <td><?php if ($secure && $clientSSLKey == $cert['certKey']): ?><i class="icon-ok-sign" title="In Use!"></i><?php endif; ?></td>
		<td><?php echo $cert['serialNumber']; ?></td>
		<td><?php echo $cert['hash']; ?></td>
		<td><?php echo $cert['subject']['organizationName']; ?></td>
		<td><?php echo Date::dayFormat($cert['validFrom_time_t']); ?></td>
		<td><?php echo Date::dayFormat($cert['validTo_time_t']); ?></td>
		<td>
			<form action="<?php echo Url::format('/user/certificate/view'); ?>" method="post" style="display: inline">
				<input type="hidden" name="hash" value="<?php echo $cert['certKey']; ?>" />
				<input type="submit" value="View" class="btn btn-info"/>
			</form>
        </td>
        <td>
			<form action="<?php echo Url::format('/user/certificate/remove'); ?>" method="post" style="display: inline">
				<input type="hidden" name="hash" value="<?php echo $cert['certKey']; ?>" />
				<input type="submit" value="Delete" class="btn btn-danger" />
			</form>
		</td>
	</tr>
<?php endforeach; ?>
</tbody></table>
<?php else: ?>
	<div class="alert alert-info">You have no certificates.</div>
<?php endif; ?>
<a href="<?php echo Url::format('pages/info/keyauthentication'); ?>">About</a><br />
<br />

<legend>Security Settings</legend>

<form class="well forum-vertical" action="<?php echo Url::format('/user/settings/saveAuth'); ?>" method="post">
	<label class="checkbox">
		<input type="checkbox" name="passwordAuth" <?php if (in_array('password', $user['auths'])): ?>checked="checked"<?php endif; ?> />&nbsp;
		Password authentication
	</label>
	
	<label class="checkbox">
		<input type="checkbox" name="certificateAuth" <?php if (in_array('certificate', $user['auths'])): ?>checked="checked"<?php endif; ?> />&nbsp;
		Certificate authentication
	</label>
	
	<label class="checkbox">
		<input type="checkbox" name="certAndPassAuth" <?php if (in_array('cert+pass', $user['auths'])): ?>checked="checked"<?php endif; ?> />&nbsp;
		Certificate and Password authentication
	</label>
	
	<label class="checkbox">
		<input type="checkbox" name="autoAuth" <?php if (in_array('autoauth', $user['auths'])): ?>checked="checked"<?php endif; ?> />&nbsp;
		Automatically authenticate you on TLS.
	</label><br />
	
	<input type="submit" value="Save" class="btn btn-primary" />&nbsp;
	<a href="<?php echo Url::format('pages/info/keyauthentication#auths'); ?>">More info...</a><br />
</form>

<?php
endif;
?>
