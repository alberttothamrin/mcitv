<main class="m-auto py-5">
  <div class="form">  
    <div id="login" class="guest">   
      <h1>Welcome Back!</h1>  
      @if( Session::has( 'success_message' ))
        <div class="alert alert-success">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get( 'success_message' ) }}
        </div>
      @endif
      
      @if($errors->login->has('email') || $errors->login->has('password') || $errors->first('invalidLogin'))
        <div class="alert alert-danger">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ $errors->login->first('email') }}
          {{ $errors->login->first('password') }}       
          {{ $errors->first('invalidLogin') }}        
       </div>
      @endif
      
      <form method="post" action="{{url('login')}}" accept-charset="UTF-8">
        @csrf
        <div class="field-wrap">
          <label>
            Email Address<span class="req">*</span>
          </label>
          <input type="email" name="email" required autocomplete="off"/>
        </div>
      
        <div class="field-wrap">
          <label>
            Password<span class="req">*</span>
          </label>
          <input type="password" name="password" required autocomplete="off"/>
        </div>
      
        <!-- <p class="forgot"><a href="#">Forgot Password?</a></p> -->
        
        <button class="button button-block"/>Log In</button>
      
      </form>

    </div>
  </div> <!-- /form -->
</main>