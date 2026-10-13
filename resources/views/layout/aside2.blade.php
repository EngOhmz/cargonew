@php
    $sidebarLogoUrl = company_logo_url();
    $sidebarBackgroundStyle = $sidebarLogoUrl
        ? "background-image: url('{$sidebarLogoUrl}'); background-size: cover !important; background-repeat: no-repeat !important; background-position: center center !important; width: 100% !important; display: block !important;"
        : "background-color: #ffffff;";
@endphp

<div class="sidebar sidebar-light sidebar-main sidebar-expand-lg">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-section" style="margin: 0 !important; padding: 0 !important; border: none !important;">
            <div class="sidebar-user-material" style="{{ $sidebarBackgroundStyle }} min-height: 180px; background-color: #f8f9fa; border: none !important; margin: 0 !important; padding: 0 !important; border-radius: 0 !important;">
                <div class="sidebar-section-body" style="padding: 0 !important; margin: 0 !important; background: rgba(0,0,0,0.1); min-height: 180px;">
                    <div class="d-flex">
                        <div class="flex-1">
                            {{-- <button type="button"
                                class="btn btn-outline-light border-transparent btn-icon btn-sm rounded-pill">
                                <i class="icon-wrench"></i>
                            </button> --}}
                        </div>
                        <div class="flex-1 text-right">
                            <button type="button"
                                class="btn btn-outline-dark border-transparent btn-icon rounded-pill btn-sm sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
                                <i class="icon-transmission"></i>
                            </button>

                            <button type="button"
                                class="btn btn-outline-dark border-transparent btn-icon rounded-pill btn-sm sidebar-mobile-main-toggle d-lg-none">
                                <i class="icon-cross2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->


        <!-- Main navigation -->
        <div class="sidebar-section">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
            
                @if(auth()->user()->hasModuleAccess('Dashboard', 'access'))
                <li class="nav-item">
                    <a href="{{url('summary/dashboard')}}"
                        class="nav-link  {{ (request()->is('summary/dashboard')) ? 'active' : ''  }}">
                        <i class="icon-home"></i>
                        <span>
                            Dashboard
                        </span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasModuleAccess('Sajili Wateja & Mizigo', 'read'))
                <li class="nav-item nav-item-submenu {{ (request()->is('home') || request()->is('list_ya_wateja*')) ? 'nav-item-expanded nav-item-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="icon-users4"></i>
                        <span>Registration</span>
                    </a>
                    <ul class="nav nav-group-sub" data-submenu-title="Registration">
                        <li class="nav-item">
                            <a href="{{url('home')}}" class="nav-link {{ (request()->is('home')) ? 'active' : ''  }}">
                                <i class="icon-plus-circle2"></i>
                                <span>Sajili Wateja & Mizigo</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('list_ya_wateja.index') }}" class="nav-link {{ (request()->is('list_ya_wateja*')) ? 'active' : ''  }}">
                                <i class="icon-list"></i>
                                <span>List ya Wateja</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->hasModuleAccess('List ya Mizigo', 'read'))
                <li class="nav-item nav-item-submenu {{ (request()->is('list_ya_mizigo')) ? 'nav-item-expanded nav-item-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="icon-package"></i>
                        <span>Cargo Management</span>
                    </a>
                    <ul class="nav nav-group-sub" data-submenu-title="Cargo Management">
                        <li class="nav-item">
                            <a href="{{url('list_ya_mizigo')}}" class="nav-link {{ (request()->is('list_ya_mizigo')) ? 'active' : ''  }}">
                                <i class="icon-list"></i>
                                <span>List ya Mizigo</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->hasModuleAccess('Sajili mikoa', 'access'))
                <li class="nav-item nav-item-submenu {{ (request()->is('mikoa*') || request()->is('cargo-routes*')) ? 'nav-item-expanded nav-item-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="icon-map5"></i>
                        <span>Mikoa & Routes</span>
                    </a>
                    <ul class="nav nav-group-sub" data-submenu-title="Mikoa & Routes">
                        <li class="nav-item">
                            <a href="{{ url('mikoa/manage') }}"
                                class="nav-link {{ (request()->is('mikoa*')) ? 'active' : '' }}">
                                <i class="icon-location4"></i>
                                <span>Sajili mikoa</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('cargo_routes.index') }}"
                                class="nav-link {{ (request()->is('cargo-routes*')) ? 'active' : '' }}">
                                <i class="icon-road"></i>
                                <span>Sajili Routes</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->hasModuleAccess('All Cars', 'read') || auth()->user()->hasModuleAccess('Drivers', 'read') || auth()->user()->hasModuleAccess('Routes & Drop-off Points', 'read'))
                <li class="nav-item nav-item-submenu {{ (request()->is('management/all_car*') || request()->is('management/active_car*') || request()->is('management/driver*') || request()->is('management/routes_overview*')) ? 'nav-item-expanded nav-item-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="icon-truck"></i>
                        <span>Fleet Management</span>
                    </a>
                    <ul class="nav nav-group-sub" data-submenu-title="Fleet Management">
                        @if(auth()->user()->hasModuleAccess('All Cars', 'read'))
                        <li class="nav-item">
                            <a href="{{ url('management/all_car') }}" class="nav-link {{ (request()->is('management/all_car')) ? 'active' : ''  }}">
                                <i class="icon-car2"></i>
                                <span>All Cars</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('active_car') }}" class="nav-link {{ (request()->is('management/active_car*')) ? 'active' : ''  }}">
                                <i class="icon-checkmark-circle2"></i>
                                <span>Active Cars</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasModuleAccess('Drivers', 'read'))
                        <li class="nav-item">
                            <a href="{{ route('driver.index') }}" class="nav-link {{ (request()->is('management/driver*')) ? 'active' : ''  }}">
                                <i class="icon-users"></i>
                                <span>Drivers</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasModuleAccess('Routes & Drop-off Points', 'read'))
                        <li class="nav-item">
                            <a href="{{ url('management/routes_overview') }}" class="nav-link {{ (request()->is('management/routes_overview*')) ? 'active' : ''  }}">
                                <i class="icon-road"></i>
                                <span>Routes & Drop-off Points</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(auth()->user()->hasModuleAccess('Delivery Agent List', 'read') || auth()->user()->hasModuleAccess('New Delivery Agent', 'read'))
                <li class="nav-item nav-item-submenu {{ (request()->is('management/delivery_agents*')) ? 'nav-item-expanded nav-item-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="icon-user-tie"></i>
                        <span>Delivery Agents</span>
                    </a>
                    <ul class="nav nav-group-sub" data-submenu-title="Delivery Agents">
                        @if(auth()->user()->hasModuleAccess('Delivery Agent List', 'read'))
                        <li class="nav-item">
                            <a href="{{ route('delivery_agents.index') }}" class="nav-link {{ (request()->is('management/delivery_agents')) ? 'active' : ''  }}">
                                <i class="icon-list"></i>
                                <span>Delivery Agent List</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasModuleAccess('New Delivery Agent', 'read'))
                        <li class="nav-item">
                            <a href="{{ route('delivery_agents.create') }}" class="nav-link {{ (request()->is('management/delivery_agents/create')) ? 'active' : ''  }}">
                                <i class="icon-plus-circle2"></i>
                                <span>New Delivery Agent</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(auth()->user()->hasModuleAccess('Revenue Report', 'read') || auth()->user()->hasModuleAccess('Car Parcels Report', 'read') || auth()->user()->hasModuleAccess('All Cars', 'read'))
                <li class="nav-item nav-item-submenu {{ (request()->is('reports*')) ? 'nav-item-expanded nav-item-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="icon-stats-bars"></i>
                        <span>Reports</span>
                    </a>
                    <ul class="nav nav-group-sub" data-submenu-title="Reports">
                        @if(auth()->user()->hasModuleAccess('Revenue Report', 'read'))
                        <li class="nav-item">
                            <a href="{{ url('reports/revenue') }}" class="nav-link {{ (request()->is('reports/revenue*')) ? 'active' : ''  }}">
                                <i class="icon-coin-dollar"></i>
                                <span>Revenue Report</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasModuleAccess('Car Parcels Report', 'read') || auth()->user()->hasModuleAccess('All Cars', 'read') || auth()->user()->hasModuleAccess('Revenue Report', 'read'))
                        <li class="nav-item">
                            <a href="{{ route('reports.car_parcels.index') }}" class="nav-link {{ (request()->is('reports/car-parcels*')) ? 'active' : ''  }}">
                                <i class="icon-truck"></i>
                                <span>Gari, Mizigo & Wateja</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(auth()->user()->hasModuleAccess('Store records', 'access'))
                <li class="nav-item">
                <a class="nav-link {{ (request()->is('management/pacel_store*')) ? 'active' : ''  }}"
                                href="{{ url('management/pacel_store') }}"> <i class="icon-list-unordered"></i>
                        <span>{{__('Store records')}}</span> </a>
                </li>
                <li class="nav-item">
                <a class="nav-link {{ (request()->is('management/outstanding_debts*')) ? 'active' : ''  }}"
                                href="{{ route('outstanding_debts') }}"> <i class="icon-coin-dollar"></i>
                        <span>Madeni ya Mizigo</span> </a>
                </li>
                <li class="nav-item">
                <a class="nav-link {{ (request()->is('management/delivery_note*') || request()->is('management/search_store*')) ? 'active' : ''  }}"
                                href="{{ route('delivery_note') }}"> <i class="icon-truck"></i>
                        <span>Delivery Note</span> </a>
                </li>
                @endif

                @php
                    use App\Support\CompanyAccess;
                    $canAccessControl = auth()->user()->hasModuleAccess('User Management', 'read') || 
                                        auth()->user()->hasModuleAccess('Change Password', 'read') || 
                                        auth()->user()->hasModuleAccess('Role', 'read') || 
                                        auth()->user()->hasModuleAccess('Upload Company Logo', 'read') || 
                                        auth()->user()->hasModuleAccess('Edit document', 'read') || 
                                        auth()->user()->hasModuleAccess('Permission', 'read') || 
                                        auth()->user()->hasModuleAccess('SMS Management', 'access') ||
                                        (auth()->check() && CompanyAccess::isPlatformAdmin(auth()->user()));
                @endphp

                @if($canAccessControl)
                <li class="nav-item nav-item-submenu"> <a href="#" class="nav-link"><i class="icon-cog7"></i> <span> Access Control</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">

                        @if(auth()->user()->hasModuleAccess('User Management', 'read'))
                        <li class=" nav-item "><a class="nav-link {{ (request()->is('users*')) ? 'active' : ''  }}" href="{{url('users')}}">User Management</a></li>
                        @endif
                       
                        @if(auth()->user()->hasModuleAccess('Change Password', 'read'))
                         <li class="nav-item">  <a class="nav-link " href="{{ url('change_password') }}">Change Password</a> </li>
                        @endif

                        @if(auth()->user()->hasModuleAccess('Role', 'read'))
                        <li class=" nav-item"><a class="nav-link {{ (request()->is('access_control/roleGroup*')) ? 'active' : ''  }}"
                                 href="{{ route('roles.index') }}">Role</a>
                        </li>
                        @endif

                        @if(auth()->user()->hasModuleAccess('Upload Company Logo', 'read'))
                        <li class=" nav-item"><a class="nav-link {{ (request()->is('company-logo')) ? 'active' : ''  }}" href="{{ route('company-logo.index') }}">Upload Company Logo</a></li>
                        @endif

                        @if(auth()->user()->hasModuleAccess('Edit document', 'read'))
                        <li class=" nav-item"><a class="nav-link {{ (request()->is('document_settings*')) ? 'active' : ''  }}" href="{{ route('document_settings.index') }}">Edit document</a></li>
                        @endif

                        @if(auth()->user()->hasModuleAccess('SMS Management', 'access') || auth()->user()->hasModuleAccess('Edit document', 'read') || (auth()->check() && \App\Support\CompanyAccess::isPlatformAdmin(auth()->user())))
                        <li class="nav-item">
                            <a href="{{ route('sms.index') }}"
                                class="nav-link {{ (request()->is('sms*')) ? 'active' : '' }}">
                                SMS Management
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->hasModuleAccess('Permission', 'read'))
                        <li class=" nav-item "><a class="nav-link {{ (request()->is('access_control/roleGroup*')) ? 'active' : ''  }}"
                            <a href="{{ route('permissions.index') }}"> Permission</a>
                        </li>
                        @endif

                         @if(auth()->check() && \App\Support\CompanyAccess::isPlatformAdmin(auth()->user()))
                        <li class=" nav-item "><a class="nav-link {{ (request()->is('registered_companies*')) ? 'active' : ''  }}" href="{{ route('registered_companies.index') }}">Registered Companies</a></li>
                        @endif

                    </ul>
                </li>
                @endif

                @if(auth()->check() && \App\Support\CompanyAccess::isPlatformAdmin(auth()->user()))
                <li class="nav-item">
                    <a href="{{ route('sms_billing.index') }}"
                        class="nav-link {{ (request()->is('sms-billing*')) ? 'active' : '' }}">
                        <i class="icon-stats-bars"></i>
                        <span>SMS Billing</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasModuleAccess('App Details', 'read'))
                <li class="nav-item nav-item-submenu"> <a href="#" class="nav-link"><i class="icon-mobile"></i> <span> Mobile Settings</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Mobile Settings">

                        <li class=" nav-item "><a class="nav-link {{ (request()->is('mobile_settings*')) ? 'active' : ''  }}" href="{{ route('mobile_settings.index') }}">App Details</a></li>

                    </ul>
                </li>
                @endif


                   

                <!-- /page kits -->


       

 

        
    
    </ul>

            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>