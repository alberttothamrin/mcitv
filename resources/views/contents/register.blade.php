<main class="m-auto py-1">
  <div class="form">  
    <div id="signup" class="guest">   
      <h1>Sign Up for Free</h1>          
      <!-- if there are login errors, show them here -->
      @if($errors->register->has('name') || $errors->register->has('email') || $errors->register->has('password'))
        <div class="alert alert-danger">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ $errors->register->first('name') }}
          {{ $errors->register->first('email') }}   
          {{ $errors->register->first('password') }}      
        </div>
      @endif

      <form method="post" action="{{url('register')}}" accept-charset="UTF-8">
        @csrf          

        <div class="field-wrap">
          <label>
            Full Name<span class="req">*</span>
          </label>
          <input type="text" name="fullname" value="{{ old('fullname') }}" required autocomplete="off" />
        </div>

        <div class="field-wrap">
          <label>
            Email Address<span class="req">*</span>
          </label>
          <input type="email" name="reg_email" value="{{ old('reg_email') }}" required autocomplete="off" />
        </div>
        
        <div class="field-wrap">
          <label>
            Set A Password<span class="req">*</span>
          </label>
          <input type="password" name="reg_password" required autocomplete="off"/>
        </div>

        <div class="field-wrap">
          <label>
            Confirm Your Password<span class="req">*</span>
          </label>
          <input type="password" name="password_confirmation" required autocomplete="off"/>
        </div>
        
        <button type="submit" class="button button-block"/>Get Started</button>
      
      </form>

    </div>
  </div>
</main>