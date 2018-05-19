@if (Auth::check())
    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
          <div class="pull-left image">
            <img src="https://placehold.it/160x160/fab80f/ffffff/&text={{ mb_substr(Auth::user()->name, 0, 1) }}" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p>{{ Auth::user()->name }}</p>
          </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
          <li class="header">{{ trans('backpack::base.administration') }}</li>
          <!-- ================================================ -->
          <!-- ==== Recommended place for admin menu items ==== -->
          <!-- ================================================ -->
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/dashboard') }}"><i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>

          <!-- ============================== -->
          <!-- ==== LMS admin menu items ==== -->
          <!-- ============================== -->
          <li class="header">LMS</li>
          <li><a href="{{ url('admin/course') }}"><i class="fa fa-book"></i> <span>Courses</span></a></li>
          <li><a href="{{ url('admin/module') }}"><i class="fa fa-files-o"></i> <span>Modules</span></a></li>
          <li><a href="{{ url('admin/lesson') }}"><i class="fa fa-files-o"></i> <span>Lessons</span></a></li>
          <li><a href="{{ url('admin/session') }}"><i class="fa fa-video-camera"></i> <span>Sessions</span></a></li>
          <li><a href="{{ url('admin/resource') }}"><i class="fa fa-file-image-o"></i> <span>Resources</span></a></li>
          <li><a href="{{ url('admin/coachingcall') }}"><i class="fa fa-university"></i> <span>Coaching Calls</span></a></li>
          <li><a href="{{ url('admin/training') }}"><i class="fa fa-university"></i> <span>Training</span></a></li>
          <li><a href="{{ url('admin/lessonquestion') }}"><i class="fa fa-question"></i> <span>Lesson questions</span></a></li>
          <li><a href="{{ url('admin/cohort') }}"><i class="fa fa-group"></i> <span>Cohorts</span></a></li>
          <li><a href="{{ url('admin/bonus') }}"><i class="fa fa-book"></i> <span>Bonus</span></a></li>
          <li><a href="{{ url('admin/reports') }}"><i class="fa fa-list-alt"></i> <span>Reports</span></a></li>
          <li><a href="{{ url('admin/schedule') }}"><i class="fa fa-list-alt"></i> <span>Schedules</span></a></li>

          <li class="header">Events</li>
          <li><a href="{{ url('admin/event') }}"><i class="fa fa-calendar"></i> <span>Events</span></a></li>

          @role('Administrator')
          <li class="header">Users</li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/user') }}"><i class="fa fa-user"></i> <span>Users</span></a></li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/user-logins') }}"><i class="fa fa-database"></i> <span>User logins</span></a></li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/role') }}"><i class="fa fa-group"></i> <span>Roles</span></a></li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/notify') }}"><i class="fa fa-commenting-o"></i> <span>Notify</span></a></li>
          @endrole

          <!-- ======================================= -->
          <li class="header">{{ trans('backpack::base.user') }}</li>
          @role('Administrator')
          <li><a href="{{ url('admin/settings') }}"><i class="fa fa-cog"></i> <span>Settings</span></a></li>
          <li><a href="{{ url('admin/log') }}"><i class="fa fa-sticky-note-o"></i> <span>Log</span></a></li>
          <li><a href="{{ url('admin/analytics') }}"><i class="fa fa-tasks"></i> <span>Analytics</span></a></li>
          <li><a href="{{ url('admin/logs') }}"><i class="fa fa-sticky-note-o"></i> <span>Activities</span></a></li>
          @endrole
          <li><a href="{{ url('admin/elfinder') }}"><i class="fa fa-files-o"></i> <span>File manager</span></a></li>
          <li><a href="{{ url('admin/survey') }}"><i class="fa fa-table"></i> <span>Survey</span></a></li>
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>
@endif
