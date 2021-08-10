 @php
 	$app_menu_=isset($h_app_)?(isset($h_app_['menu'])?$h_app_['menu']:null):null;
 @endphp
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item {{$app_menu_=='dashboard'?'active':null}}">
        <a class="nav-link"  href="{{route('dash.app.detail',['uuid'=>$app->uuid])}}">DASHBOARD </a>
      </li>
      <li class="nav-item {{$app_menu_=='contact'?'active':null}}">
        <a class="nav-link" href="{{route('dash.contact.index',['uuid'=>$app->uuid])}}">CONTACT</a>
      </li>
      <li class="nav-item {{$app_menu_=='group'?'active':null}}">
        <a class="nav-link" href="{{route('dash.group.index',['uuid'=>$app->uuid])}}">GROUPS</a>
      </li>
      <li class="nav-item {{$app_menu_=='message'?'active':null}}">
        <a class="nav-link" href="{{route('dash.app.message',['uuid'=>$app->uuid])}}">MESSAGE</a>
      </li>
        <li class="nav-item {{$app_menu_=='setting'?'active':null}}">
        <a class="nav-link" href="#">SETTING</a>
      </li>
     
    </ul>
  </div>
</nav>