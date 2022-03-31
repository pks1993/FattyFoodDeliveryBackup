<nav class="main-header navbar navbar-expand navbar-white">
  <!-- Left navbar links -->
  <ul class="navbar-nav mr-auto">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" style="color: #000000;"><i class="fas fa-bars"></i></a>
    </li>
    {{-- <li class="nav-item d-none d-sm-inline-block">
      <a href="{{url('fatty/main/admin/dashboard')}}" class="nav-link">
        <img src="{{asset('logo/user_logo.png')}}" alt="" class="brand-image img-circle" style="margin-top: -8px;width: 50px;height: 50px;">
      </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <b>
        <a href="{{url('fatty/main/admin/dashboard')}}" class="nav-link" style="font-size: 25px;color: #FF6604;margin-bottom: 12px;">Fatty Application</a>
      </b>
    </li> --}}
  </ul>

  <!-- Right navbar links -->
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-fixed-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" style="color: #000000;" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" style="color: #000000" data-toggle="dropdown">
                        <span class="hidden-xs">{{Auth::user()->name}}</span>
                        <span class="hidden-xs">
                            @if(!empty(Auth::user()->image))
                                <img src="/uploads/user/{{Auth::user()->image}}" class="img-circle" style="width:40px;height: 40px;">
                            @else
                                <img src="/uploads/user/User.png" class="img-circle" style="width:40px;height: 40px;">
                            @endif
                        </span>
                    </a>
                    <ul class="dropdown-menu" style="background-color: #000340;color: rgb(255, 255, 255);">
                        <!-- User image -->
                        <li class="user-header">
                            <span class="hidden-xs">
                                @if(!empty(Auth::user()->image))
                                    <img src="/uploads/user/{{Auth::user()->image}}" class="img-circle" style="width:90px;height: 90px;">
                                @else
                                    <img src="/uploads/user/User.png" class="img-circle" style="width:90px;height: 90px;">
                                @endif
                                <p>
                                    <small>
                                        {{Auth::user()->name}}
                                    </small></br>
                                    <small>
                                        Member since {{date('d-M-Y', strtotime(Auth::User()->created_at))}}
                                    </small>
                                </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ asset('fatty/main/admin/user') }}" class="btn btn-outline-primary" style="color: black" ><i class="fa fa-cog"></i> Setting</a>
                            </div>
                            <div class="pull-right">
                              <form id="frm-logout" action="{{ route('fatty.admin.logout') }}" method="POST" style="display: none;">
                                @csrf
                              </form>
                              <a href="{{route('fatty.admin.logout')}}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" class="nav-link btn btn-outline-danger btn-sm" style="color: rgb(000, 000, 000);">
                                  <i class="fa fa-sign-out" aria-hidden="true"></i>Logout
                              </a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

    </nav>
</nav>
