<h2><div class="bodytitle_l"></div>
<div class="bodytitle_r"></div>
user registration</h2>
      <form id="registration" name="form5" method="post" action="">
      	<?php
      	if(count($_POST)>0 && $_POST['action']=="register") {
      		include 'modules/swi_reg_mdl/class/Actions.php';
      		$actions = new Actions();
      		$params = $_POST;
      		echo '<pre>';
      		print_r($actions->register($params));
      		echo '</pre>';
      	}
      	?>
        <div class="formrow"> 
          <div class="message">
            <p>*all fields are required</p>
          </div>
        </div>
        <div class="formrow">
            <a class="help" href="#">?</a>
  <label for="label">email:</label>
  <input name="email" type="text" class="large" id="label" />
  <div class="msgfield">this is a required field</div>
        </div>        
        <div class="formrow">
            <a class="help" href="#">?</a>
  <label for="label">username/nick:</label>
  <input name="username" type="text" class="large" id="label" />
  <div class="msgfield">this is a required field</div>
        </div>
          <div class="formrow"> <a class="help" href="#">?</a>
            <label for="label2">password/pin:</label>
              <input name="password" type="password" class="large" id="label2" />
            <div class="msgfield">this is a required field</div>
          </div>
  <div class="buttonbar">
    <input type="submit" name="button4" id="button4" value="register" />
	</div>
	<input name="action" type="hidden"  value="register"/>
      </form>