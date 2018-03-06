<header class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container-fluid">
        <div class="overlay"></div>
        <div class="active">        
            <a class="navbar-brand" href="{{route('root')}}">
            <img alt="Brand" src="{{URL::asset('public/images/logo.png')}}" width="50">
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            @guest
                <li><a class="nav-link {{Request::segment(1) == 'login'?'active':''}}" href="{{Route('login')}}">Login</a></li>
                <li><a class="nav-link {{Request::segment(1) == 'register'?'active':''}}" href="{{Route('register')}}">Register</a></li>
            @else
                <li><a class="nav-link {{Request::segment(1) == 'dashboard'?'active':''}}" href="dashboard">Dashboard</a></li>
                <li><a href="{{url('help')}}">Help</a></li>
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }}&nbsp;
                        @if($newnotification->count() > 0)
                        <sup><span class="badge new notif-badge">{{$newnotification->count()}}</span></sup>
                        @endif
                        <span class="caret"></span>
                    </a>                
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#" onclick="javascript:updateNotifStatus(this, '{{ Auth::user()->id}}',  $('meta[name=\'csrf-token\']').attr('content'), '{{url("checknotif")}}')">
                            Notification <span class="badge notif-badge">{{$newnotification->count()}}</span>
                        </a>
                        <a class="dropdown-item" href="{{Route('do.logout') }}"
                           onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{Route('do.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
            
          </ul>
        </div>
      </div>
    </div>
</header>
