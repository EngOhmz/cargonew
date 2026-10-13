<div class="sidebar sidebar-light sidebar-main sidebar-expand-lg">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-section">
            <div class="sidebar-user-material">
                <div class="sidebar-section-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <button type="button"
                                class="btn btn-outline-light border-transparent btn-icon btn-sm rounded-pill">
                                <i class="icon-wrench"></i>
                            </button>
                        </div>
                        <a href="#" class="flex-1 text-center"><img
                                src="../../../../global_assets/images/placeholders/placeholder.jpg"
                                class="img-fluid rounded-circle shadow-sm" width="80" height="80" alt=""></a>
                        <div class="flex-1 text-right">
                            <button type="button"
                                class="btn btn-outline-light border-transparent btn-icon rounded-pill btn-sm sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
                                <i class="icon-transmission"></i>
                            </button>

                            <button type="button"
                                class="btn btn-outline-light border-transparent btn-icon rounded-pill btn-sm sidebar-mobile-main-toggle d-lg-none">
                                <i class="icon-cross2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-center">
                        <h6 class="mb-0 text-white text-shadow-dark mt-3">Victoria Baker</h6>
                        <span class="font-size-sm text-white text-shadow-dark">Santa Ana, CA</span>
                    </div>
                </div>

                <div class="sidebar-user-material-footer">
                    <a href="#user-nav"
                        class="d-flex justify-content-between align-items-center text-shadow-dark dropdown-toggle"
                        data-toggle="collapse"><span>My account</span></a>
                </div>
            </div>

            <div class="collapse border-bottom" id="user-nav">
                <ul class="nav nav-sidebar">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="icon-user-plus"></i>
                            <span>My profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="icon-coins"></i>
                            <span>My balance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="icon-comment-discussion"></i>
                            <span>Messages</span>
                            <span class="badge badge-teal badge-pill align-self-center ml-auto">58</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="icon-cog5"></i>
                            <span>Account settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <!-- <a href="{{ route('logout') }}" class="nav-link">
                            <i class="icon-switch2"></i>
                            <span>Logout</span>
                        </a> -->
                        <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /user menu -->


        <!-- Main navigation -->
        <div class="sidebar-section">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item-header">
                    <div class="text-uppercase font-size-xs line-height-xs mt-1">Main</div> <i class="icon-menu"
                        title="Main"></i>
                </li>
                <li class="nav-item">
                    <a href="{{url('home')}}"
                        class="nav-link  {{ (request()->is('home')) ? 'active' : ''  }}">
                        <i class="icon-home"></i>
                        <span>
                            Dashboard
                        </span>
                    </a>
                </li>


                @can('view-dashoard')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('apps/*')) ? 'active' : ''  }} "><i
                            class="icon-copy"></i> <span>{{__('Apps')}}</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="{{__('Apps')}}">


                        
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps/calendar*')) ? 'active' : ''  }} "
                                href="{{url('apps/calendar/')}}">{{__('Calendar')}}</a></li>
                        
                        
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps/chat*')) ? 'active' : ''  }}"
                                href="{{url('apps/chat')}}">{{__('Chat')}}</a></li>
                        
                       
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps/contacts*')) ? 'active' : ''  }}"
                                href="{{url('apps/contacts')}}">{{__('Contacts')}}</a>
                        </li>

                        <a href="#" class="nav-link {{ (request()->is('apps/companies/*')) ? 'active' : ''  }} "><i
                            class="icon-copy"></i> <span>{{__('Companies')}}</span></a>

                                 <ul class="nav nav-group-sub" data-submenu-title="{{__('Companies')}}">

                                 <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps//companies/list*')) ? 'active' : ''  }}"
                                href="{{url('apps//companies/list')}}">{{__('List')}}</a>
                                
                                </li>

                                 <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps/companies/company-details*')) ? 'active' : ''  }}"
                                href="{{url('apps/companies/company-details')}}">{{__('Company Details')}}</a>
                                
                                </li>

                                 </ul>
                        </li>

                        <li class="nav-item nav-item-submenu">


                        <a href="#" class="nav-link {{ (request()->is('apps/ecommerce/*')) ? 'active' : ''  }} "><i
                            class="icon-copy"></i> <span>{{__('Ecommerce')}}</span></a>

                                 <ul class="nav nav-group-sub" data-submenu-title="{{__('Ecommerce')}}">

                                 <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps//ecommerce/dashboard*')) ? 'active' : ''  }}"
                                href="{{url('apps//companies/dashboard')}}">{{__('Dashboard')}}</a>
                                
                                </li>

                                 <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps/ecommerce/products*')) ? 'active' : ''  }}"
                                href="{{url('apps/companies/products')}}">{{__('Company Details')}}</a>
                                
                                </li>

                                <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps//ecommerce/product-details*')) ? 'active' : ''  }}"
                                href="{{url('apps//companies/product-details')}}">{{__('Product Details')}}</a>
                                
                                </li>

                                 <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps/ecommerce/add-product*')) ? 'active' : ''  }}"
                                href="{{url('apps/companies/add-product')}}">{{__('Add Product')}}</a>
                                
                                </li>

                                <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps//ecommerce/orders*')) ? 'active' : ''  }}"
                                href="{{url('apps//companies/orders')}}">{{__('Orders')}}</a>
                                
                                </li>

                                 <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps/ecommerce/order-details*')) ? 'active' : ''  }}"
                                href="{{url('apps/companies/order-details')}}">{{__('Order Details')}}</a>
                                
                                </li>

                                <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps//ecommerce/customers*')) ? 'active' : ''  }}"
                                href="{{url('apps//companies/customers')}}">{{__('Customers')}}</a>
                                
                                </li>

                                 <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps/ecommerce/cart*')) ? 'active' : ''  }}"
                                href="{{url('apps/companies/cart')}}">{{__('Cart')}}</a>
                                
                                </li>

                                <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps//ecommerce/sellers*')) ? 'active' : ''  }}"
                                href="{{url('apps//companies/sellers')}}">{{__('Sellers')}}</a>
                                
                                </li>

                                 <li class="nav-item"><a
                                class="nav-link {{ (request()->is('apps/ecommerce/checkout*')) ? 'active' : ''  }}"
                                href="{{url('apps/companies/checkout')}}">{{__('Checkout')}}</a>
                                
                                </li>

                                 </ul>
                        </li>
                        

                    </ul>
                </li>
                @endcan

                <!-- @can('manage-farming')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('farmings/*')) ? 'active' : ''  }}"><i
                            class="icon-copy"></i> <span>{{__('farming.farming')}}</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="{{__('farming.farming')}}">


                        @can('view-manage-farming')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farmings/crop_type*')) ? 'active' : ''  }}"
                                href="{{url('farmings/crop_type')}}">Crop Type</a></li>
                        @endcan
                        @can('view-manage-farming')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farmings/seed_type*')) ? 'active' : ''  }}"
                                href="{{url('farmings/seed_type')}}">Seed Type</a></li>
                        @endcan
                        @can('view-manage-farming')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farmings/pesticide_type*')) ? 'active' : ''  }}"
                                href="{{url('farmings/pesticide_type')}}">Pesticide Type</a>
                        </li>
                        @endcan
                        @can('view-view-farmer-assets')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farmings/register_assets*')) ? 'active' : ''  }}"
                                href="{{url('farmings/register_assets')}}">{{__('farming.farmer_assets')}}</a></li>
                        @endcan
                        @can('view-view-farming-cost')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farmings/farming_cost*')) ? 'active' : ''  }}"
                                href="{{url('farmings/farming_cost')}}">{{__('farming.farming_cost')}}</a></li>
                        @endcan
                        @can('view-view-cost-centre')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farmings/cost_centre*')) ? 'active' : ''  }}"
                                href="{{url('farmings/cost_centre')}}">{{__('farming.cost_centre')}}</a></li>
                        @endcan
                        @can('view-view-farming-process')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farmings/farming_process*')) ? 'active' : ''  }}"
                                href="{{url('farmings/farming_process')}}">GAP</a></li>
                        @endcan
                        @can('view-view-crop-monitoring')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farmings/crops_monitoring*')) ? 'active' : ''  }}"
                                href="{{url('farmings/crops_monitoring')}}">{{__('farming.crop_monitoring')}}</a>
                        </li>
                        @endcan
                        @can('view-manage-farming')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farmings/lime_base*')) ? 'active' : ''  }}"
                                href="{{url('farmings/lime_base')}}">Lime Base</a></li>
                        @endcan
                        @can('view-manage_seasson')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('farming_season/seasson*')) ? 'active' : ''  }}"
                                href="{{url('farming_season/seasson')}}">{{__('farming.manage_seasson')}}</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan -->

                @can('manage-orders1')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('orders/*')) ? 'active' : ''  }}"><i
                            class="icon-copy"></i> <span>{{__('ordering.orders')}}</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-order_list')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('orders/orders*')) ? 'active' : ''  }}"
                                href="{{url('orders/orders')}}">{{__('ordering.order_list')}}</a></li>
                        @endcan
                        @can('view-quotation-list')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('orders/quotationList*')) ? 'active' : ''  }}"
                                href="{{url('orders/quotationList')}}">{{__('ordering.quotationList')}}</a></li>
                        @endcan
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('crop_order/crops_order*')) ? 'active' : ''  }}"
                                href="{{url('crop_order/crops_order')}}">Create Order</a></li>
                    </ul>
                </li>
                @endcan


                @can('view-cargo-list')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('pacel/*')) ? 'active' : ''  }} "><i
                            class="icon-package"></i> <span>Cargo Management</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-cargo-list')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('pacel/pacel_list*')) ? 'active' : ''  }}"
                                href="{{url('pacel/pacel_list')}}">Item List</a></li>
                        @endcan
                        @can('view-cargo-client-list')
                        <li class="nav-item"><a class="nav-link {{ (request()->is('pacel/client*')) ? 'active' : ''  }}"
                                href="{{url('pacel/client')}}">Client List</a></li>
                        @endcan
                        @can('view-cargo-quotation')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('pacel/pacel_quotation*')) ? 'active' : ''  }}"
                                href="{{url('pacel/pacel_quotation')}}">Quotation</a></li>
                        @endcan
                        @can('view-cargo-invoice')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('pacel/pacel_invoice*')) ? 'active' : ''  }}"
                                href="{{url('pacel/pacel_invoice')}}">Invoice</a></li>
                        @endcan
                        @can('view-cargo-mileage')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('pacel/mileage*')) ? 'active' : ''  }}"
                                href="{{url('pacel/mileage')}}">Mileage List</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan


                @can('manage-orders')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('tracking/*')) ? 'active' : ''  }}"><i
                            class="icon-cart"></i> <span>Cargo Tracking</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-cargo-collection')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tracking/collection*')) ? 'active' : ''  }}"
                                href="{{url('tracking/collection')}}"> Cargo List</a></li>
                        @endcan
                        @can('view-cargo-loading')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tracking/loading*')) ? 'active' : ''  }}"
                                href="{{url('tracking/loading')}}"> Loading</a></li>
                        @endcan
                        @can('view-cargo-offloading')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tracking/offloading*')) ? 'active' : ''  }}"
                                href="{{url('tracking/offloading')}}"> Offloading</a></li>
                        @endcan
                        @can('view-cargo-delivering')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tracking/delivering*')) ? 'active' : ''  }}"
                                href="{{url('tracking/delivering')}}">Delivery</a></li>
                        @endcan
                        @can('view-cargo-wb')
                        <li class="nav-item"><a class="nav-link {{ (request()->is('tracking/wb*')) ? 'active' : ''  }}"
                                href="{{url('tracking/wb')}}">Create WB</a></li>
                        @endcan
                        @can('view-cargo-activity')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tracking/activity*')) ? 'active' : ''  }}"
                                href="{{url('tracking/activity')}}">Track Logistic Activity</a>
                        </li>
                        @endcan
                        @can('view-cargo-order_report')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tracking/order_report*')) ? 'active' : ''  }}"
                                href="{{url('tracking/order_report')}}">Uplift Report</a></li>
                        @endcan
                        @can('view-cargo-truck_mileage')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tracking/truck_mileage*')) ? 'active' : ''  }}"
                                href="{{url('tracking/truck_mileage')}}">Return Truck Fuel &
                                Mileage</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan


                @can('manage-courier')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('courier/*')) ? 'active' : ''  }}"><i
                            class="icon-copy"></i> <span>Courier Management</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-courier_list')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_list*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_list')}}">Item List</a></li>
                        @endcan
                        @can('view-courier_client')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_client*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_client')}}">Client List</a></li>
                        @endcan
                        @can('view-courier_quotation')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_quotation*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_quotation')}}">Quotation</a></li>
                        @endcan
                        @can('view-courier_invoice')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_invoice*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_invoice')}}">Invoice</a></li>
                        @endcan

                          @can('view-courier_collection')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_collection*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_collection')}}"> Courier
                                Collection</a></li>
                        @endcan
                        @can('view-courier_loading')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_loading*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_loading')}}"> Courier Loading</a>
                        </li>
                        @endcan
                        @can('view-courier_offloading')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_offloading*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_offloading')}}"> Courier
                                Offloading</a></li>
                        @endcan
                        @can('view-courier_delivering')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_delivering*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_delivering')}}"> Courier
                                Delivery</a></li>
                        @endcan
                        @can('view-courier_activity')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_activity*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_activity')}}">Track Courier
                                Activity</a></li>
                        @endcan
                        @can('view-courier_activity')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier/courier_activity*')) ? 'active' : ''  }}"
                                href="{{url('courier/courier_activity')}}"> Courier Uplift
                                Report</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- @can('manage-courier')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('courier_tracking/*')) ? 'active' : ''  }}"><i
                            class="icon-copy"></i> <span>Courier Tracking</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-courier_collection')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier_tracking/courier_collection*')) ? 'active' : ''  }}"
                                href="{{url('courier_tracking/courier_collection')}}"> Courier
                                Collection</a></li>
                        @endcan
                        @can('view-courier_loading')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier_tracking/courier_loading*')) ? 'active' : ''  }}"
                                href="{{url('courier_tracking/courier_loading')}}"> Courier Loading</a>
                        </li>
                        @endcan
                        @can('view-courier_offloading')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier_tracking/courier_offloading*')) ? 'active' : ''  }}"
                                href="{{url('courier_tracking/courier_offloading')}}"> Courier
                                Offloading</a></li>
                        @endcan
                        @can('view-courier_delivering')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier_tracking/courier_delivering*')) ? 'active' : ''  }}"
                                href="{{url('courier_tracking/courier_delivering')}}"> Courier
                                Delivery</a></li>
                        @endcan
                        @can('view-courier_activity')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier_tracking/courier_activity*')) ? 'active' : ''  }}"
                                href="{{url('courier_tracking/courier_activity')}}">Track Courier
                                Activity</a></li>
                        @endcan
                        @can('view-courier_activity')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('courier_tracking/courier_activity*')) ? 'active' : ''  }}"
                                href="{{url('courier_tracking/courier_activity')}}"> Courier Uplift
                                Report</a></li>
                        @endcan
                    </ul>
               
               
                </li>
                @endcan -->


                @can('manage-payroll')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('payroll/*')) ? 'active' : ''  }}"><i
                            class="icon-calculator"></i> <span>Payroll</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-salary_template')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('payroll/salary_template*')) ? 'active' : ''  }}"
                                href="{{url('payroll/salary_template')}}"> Salary
                                Template</a></li>
                        @endcan
                        @can('view-manage_salary')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('payroll/manage_salary*')) ? 'active' : ''  }}"
                                href="{{url('payroll/manage_salary')}}"> Manage
                                Salary</a></li>
                        @endcan
                        @can('view-employee_salary_list')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('payroll/employee_salary_list*')) ? 'active' : ''  }}"
                                href="{{ url('payroll/employee_salary_list') }}">
                                Employee Salary List</a>
                        </li>
                        @endcan
                        @can('view-make_payment')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('payroll/make_payment*')) ? 'active' : ''  }}"
                                href="{{url('payroll/make_payment')}}">Make Payment</a>
                        </li>
                        @endcan
                        @can('view-generate_payslip')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('payroll/generate_payslip*')) ? 'active' : ''  }}"
                                href="{{url('payroll/generate_payslip')}}">Generate
                                Payslip</a></li>
                        @endcan
                        @can('view-payroll_summary')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('payroll/payroll_summary*')) ? 'active' : ''  }}"
                                href="{{url('payroll/payroll_summary')}}">Payroll
                                Summary</a></li>
                        @endcan
                        @can('view-advance_salary')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('payroll/advance_salary*')) ? 'active' : ''  }}"
                                href="{{url('payroll/advance_salary')}}">Advance
                                Salary</a></li>
                        @endcan
                        @can('view-employee_loan')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('payroll/employee_loan*')) ? 'active' : ''  }}"
                                href="{{url('payroll/employee_loan')}}">Employee
                                Loan</a></li>
                        @endcan
                        @can('view-overtime')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('payroll/overtime*')) ? 'active' : ''  }}"
                                href="{{url('payroll/overtime')}}">Overtime</a></li>
                        @endcan
                        @can('view-nssf')
                        <li class="nav-item"><a class="nav-link {{ (request()->is('payroll/nssf*')) ? 'active' : ''  }}"
                                href="{{url('payroll/nssf')}}">Social Security (NSSF)
                            </a></li>
                        @endcan
                        @can('view-tax')
                        <li class="nav-item"><a class="nav-link {{ (request()->is('payroll/tax*')) ? 'active' : ''  }}"
                                href="{{url('payroll/tax')}}">Tax </a></li>
                        @endcan
                        @can('view-nhif')
                        <li class="nav-item"><a class="nav-link {{ (request()->is('payroll/nhif*')) ? 'active' : ''  }}"
                                href="{{url('payroll/nhif')}}">Health Contribution</a>
                        </li>
                        @endcan
                        @can('view-wcf')
                        <li class="nav-item"><a class="nav-link {{ (request()->is('payroll/wcf*')) ? 'active' : ''  }}"
                                href="{{url('payroll/wcf')}}">WCF Contribution</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- @can('manage-warehouse')
                <li class="nav-item"><a
                        class="nav-link {{ (request()->is('warehouse_management/warehouse*')) ? 'active' : ''  }}"
                        href="{{url('warehouse_management/warehouse')}}"><i data-feather="command"></i>Warehouse</a>
                </li>
                @endcan -->

                @can('view-supplier')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('purchases/*')) ? 'active' : ''  }}"><i
                            class="icon-basket"></i> <span>Purchases
                        </span></a>
                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                    <li class="nav-item"><a class="nav-link {{ (request()->is('purchases/supplier*')) ? 'active' : ''  }}"
                        href="{{url('purchases/supplier')}}"><i></i></i>Suppliers</a></li>
                </li>
                    </ul>
              

                @endcan


                @can('manage-inventory')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('tyre/*')) ? 'active' : ''  }}"><i
                            class="icon-car"></i> <span>Tire
                            Management</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-tyre_brand')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tyre/tyre_brand*')) ? 'active' : ''  }}"
                                href="{{url('tyre/tyre_brand')}}">Tire Brand</a></li>
                        @endcan
                        @can('view-purchase_tyre')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tyre/purchase_tyre*')) ? 'active' : ''  }}"
                                href="{{url('tyre/purchase_tyre')}}">Purchase Tire</a></li>
                        @endcan
                        @can('view-tyre_list')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tyre/tyre_list*')) ? 'active' : ''  }}"
                                href="{{url('tyre/tyre_list')}}">Tire List</a></li>
                        @endcan
                        @can('view-assign_truck')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tyre/assign_truck*')) ? 'active' : ''  }}"
                                href="{{url('tyre/assign_truck')}}">Assign Truck</a></li>
                        @endcan
                        @can('view-tyre_return')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tyre/tyre_return*')) ? 'active' : ''  }}"
                                href="{{url('tyre/tyre_return')}}">Tire Return</a></li>
                        @endcan
                        @can('view-tyre_reallocation')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tyre/tyre_reallocation*')) ? 'active' : ''  }}"
                                href="{{url('tyre/tyre_reallocation')}}">Tire
                                Reallocation</a></li>
                        @endcan
                        @can('view-tyre_disposal')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('tyre/tyre_disposal*')) ? 'active' : ''  }}"
                                href="{{url('tyre/tyre_disposal')}}">Tire Disposal</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @can('manage-inventory')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('inventory/*')) ? 'active' : ''  }}"><i
                            class="icon-hammer2"></i> <span>
                            Inventory</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-location')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/location*')) ? 'active' : ''  }}"
                                href="{{url('inventory/location')}}">Location</a></li>
                        @endcan
                        @can('view-inventory')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/inventory*')) ? 'active' : ''  }}"
                                href="{{url('inventory/inventory')}}">Inventory Items</a></li>
                        @endcan
                        @can('view-fieldstaff')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/fieldstaff*')) ? 'active' : ''  }}"
                                href="{{url('inventory/fieldstaff')}}">Field Staff</a></li>
                        @endcan
                        @can('view-service')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/requisition*')) ? 'active' : ''  }}"
                                href="{{url('inventory/requisition')}}">Requisition</a></li>
                        @endcan
                        @can('view-purchase_inventory')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/purchase_inventory*')) ? 'active' : ''  }}"
                                href="{{url('inventory/purchase_inventory')}}">Purchase
                                Inventory</a></li>
                        @endcan
                        @can('view-inventory_list')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/inventory_list*')) ? 'active' : ''  }}"
                                href="{{url('inventory/inventory_list')}}">Inventory List</a>
                        </li>
                        @endcan
                        @can('view-inventory_list')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/service_type*')) ? 'active' : ''  }}"
                                href="{{url('inventory/service_type')}}">Service Type</a></li>
                        @endcan
                        @can('view-maintainance')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/maintainance*')) ? 'active' : ''  }}"
                                href="{{url('inventory/maintainance')}}">Maintainance</a></li>
                        @endcan
                        @can('view-service')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/service*')) ? 'active' : ''  }}"
                                href="{{url('inventory/service')}}">Service</a></li>
                        @endcan
                        @can('view-service')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_issue*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_issue')}}">Good Issue</a></li>
                        @endcan
                        @can('view-good_return')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_return*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_return')}}">Good Return</a></li>
                        @endcan
                        @can('view-good_return')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_movement*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_movement')}}">Good Movement</a></li>
                        @endcan
                        @can('view-good_reallocation')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_reallocation*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_reallocation')}}">Good
                                Reallocation</a></li>
                        @endcan
                        @can('view-good_disposal')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_disposal*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_disposal')}}">Good Disposal</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan


                <!-- @can('manage-farmer')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('manufacturing/*')) ? 'active' : ''  }}"><i
                            class="icon-copy"></i> <span>
                            Manufacturing</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-location')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('manufacturing/manufacturing_location*')) ? 'active' : ''  }}"
                                href="{{url('manufacturing/manufacturing_location')}}">Location</a>
                        </li>
                        @endcan
                        @can('view-inventory')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('manufacturing/manufacturing_inventory*')) ? 'active' : ''  }}"
                                href="{{url('manufacturing/manufacturing_inventory')}}">Inventory
                                Items</a></li>
                        @endcan
                        @can('view-fieldstaff')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/fieldstaff*')) ? 'active' : ''  }}"
                                href="{{url('inventory/fieldstaff')}}">Field Staff</a></li>
                        @endcan
                        @can('view-purchase_inventory')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('manufacturing/bill_of_material*')) ? 'active' : ''  }}"
                                href="{{url('manufacturing/bill_of_material')}}">Bill Of Material</a>
                        </li>
                        @endcan
                        @can('view-purchase_inventory')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('manufacturing/work_order*')) ? 'active' : ''  }}"
                                href="{{url('manufacturing/work_order')}}">Work Order</a></li>
                        @endcan
                        @can('view-purchase_inventory')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/purchase_inventory*')) ? 'active' : ''  }}"
                                href="{{url('inventory/purchase_inventory')}}">Purchase Inventory</a>
                        </li>
                        @endcan
                        @can('view-inventory_list')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/inventory_list*')) ? 'active' : ''  }}"
                                href="{{url('inventory/inventory_list')}}">Inventory List</a>
                        </li>
                        @endcan
                        @can('view-maintainance')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/maintainance*')) ? 'active' : ''  }}"
                                href="{{url('inventory/maintainance')}}">Maintainance</a></li>
                        @endcan
                        @can('view-service')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/service*')) ? 'active' : ''  }}"
                                href="{{url('inventory/service')}}">Service</a></li>
                        @endcan
                        @can('view-service')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_issue*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_issue')}}">Good Issue</a></li>
                        @endcan
                        @can('view-good_return')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_return*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_return')}}">Good Return</a></li>
                        @endcan
                        @can('view-good_return')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_movement*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_movement')}}">Good Movement</a></li>
                        @endcan
                        @can('view-good_reallocation')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_reallocation*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_reallocation')}}">Good
                                Reallocation</a></li>
                        @endcan
                        @can('view-good_disposal')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('inventory/good_disposal*')) ? 'active' : ''  }}"
                                href="{{url('inventory/good_disposal')}}">Good Disposal</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan -->


                @can('manage-cotton')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('cotton_collection/*')) ? 'active' : ''  }}"><i
                            class="icon-copy"></i> <span>Cotton Collection</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Cotton Collection">
                        @can('view-top-up-operator')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/top_up_operator*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/top_up_operator')}}">Top up Operators</a>

                        </li>
                        @endcan
                        @can('view-top-up-center')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/top_up_center*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/top_up_center')}}">Top up Collection
                                Center</a></li>
                        @endcan


                        @can('view-cotton-purchase')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/purchase_cotton*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/purchase_cotton')}}">Stock Control</a>
                        </li>
                        @endcan
                        @can('view-cotton-movement')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/cotton_movement*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/cotton_movement')}}">Stock Movement</a>
                        </li>
                        @endcan
                        @can('view-reverse-top-up-center')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/reverse_top_up_center*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/reverse_top_up_center')}}"> Reversed
                                Collection Center</a></li>
                        @endcan
                        @can('view-reverse-top-up-operator')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/reverse_top_up_operator*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/reverse_top_up_operator')}}"> Reversed
                                Operator </a></li>
                        @endcan

                        @can('view-district')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/district*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/district')}}"> Manage District </a></li>
                        @endcan
                        @can('view-operator')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/operator*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/operator')}}">Manage Operator</a></li>
                        @endcan
                        @can('view-center')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/collection_center*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/collection_center')}}">Manage Collection
                                Center</a></li>
                        @endcan
                        @can('view-items')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/cotton_list*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/cotton_list')}}">Stock List</a></li>
                        @endcan
                        @can('view-items')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/levy_list*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/levy_list')}}">Manage Levy</a></li>
                        @endcan

                        @can('view-reverse-top-up-operator')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/complete_operator*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/complete_operator')}}"> Complete Top Up
                                Operator </a></li>
                        @endcan
                        @can('view-reverse-top-up-center')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/complete_center*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/complete_center')}}"> Complete Top Up
                                Centers</a></li>
                        @endcan
                        @can('view-connect')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/assign_center*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/assign_center')}}">Assign Equipment to
                                Center</a></li>
                        @endcan
                        @can('view-connect')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/reverse_assign_center*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/reverse_assign_center')}}">Reversed Center
                                Equiment</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan


                @can('manage-cotton')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('cotton_production/*')) ? 'active' : ''  }}"><i
                            class="icon-copy"></i> <span>
                            Cotton Production</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Cotton Production">


                        @can('view-top-up-operator')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_production/costants*')) ? 'active' : ''  }}"
                                href="{{url('cotton_production/costants')}}">Constants</a></li>
                        @endcan
                        @can('view-top-up-center')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_production/production*')) ? 'active' : ''  }}"
                                href="{{url('cotton_production/production')}}">Make Production</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @can('manage-cotton')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('cotton_invoice/*')) ? 'active' : ''  }}"><i
                            class="icon-copy"></i> <span>
                            Invoice</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Invoice">

                        @can('view-cotton-invoice')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_invoice/cotton_sales*')) ? 'active' : ''  }}"
                                href="{{url('cotton_invoice/cotton_sales')}}">Cotton Sales</a></li>
                        @endcan
                        @can('view-seed-invoice')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_invoice/seed_list*')) ? 'active' : ''  }}"
                                href="{{url('cotton_invoice/seed_list')}}">Seed List</a></li>
                        @endcan
                        @can('view-seed-invoice')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_invoice/seed_sales*')) ? 'active' : ''  }}"
                                href="{{url('cotton_invoice/seed_sales')}}">Seed Sales</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan



                @can('manage-logistic')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('logistic_truck/*')) ? 'active' : ''  }}"><i
                            class="icon-truck"></i> <span>
                            Truck & Driver</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">

                        @can('view-truck')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('logistic_truck/truck*')) ? 'active' : ''  }}"
                                href="{{url('logistic_truck/truck')}}">Truck Management</a></li>
                        @endcan
                        @can('view-driver')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('logistic_driver/driver*')) ? 'active' : ''  }}"
                                href="{{url('logistic_driver/driver')}}">Driver Management</a></li>
                        @endcan
                        @can('view-fuel')
                        <li class="nav-item"><a class="nav-link {{ (request()->is('fuel/fuel*')) ? 'active' : ''  }}"
                                href="{{url('fuel/fuel')}}">Fuel Control</a></li>
                        @endcan
                        @can('view-connect')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('logistic_truck/connect_driver*')) ? 'active' : ''  }}"
                                href="{{url('logistic_truck/connect_driver')}}">Assign & Remove
                                Driver</a></li>
                        @endcan
                        @can('view-connect')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('logistic_truck/truck_report*')) ? 'active' : ''  }}"
                                href="{{url('logistic_truck/truck_report')}}">Truck Report</a></li>
                        @endcan
                        @can('view-connect')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('logistic_truck/connect_trailer*')) ? 'active' : ''  }}"
                                href="{{url('logistic_truck/connect_trailer')}}">Connect & Disconnect
                                Trailer</a></li>
                        @endcan
                        @can('view-connect')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/assign_driver*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/assign_driver')}}">Assign Equipment to
                                Truck</a></li>
                        @endcan
                        @can('view-connect')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('cotton_collection/reverse_assign_driver*')) ? 'active' : ''  }}"
                                href="{{url('cotton_collection/reverse_assign_driver')}}">Reversed Truck
                                Equipment</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @can('view-leave')
                <li class="nav-item"><a class="nav-link {{ (request()->is('leave/leave*')) ? 'active' : ''  }}"
                        href="{{url('leave/leave')}}"><i class="icon-airplane3"></i>Leave
                        Management</a></li>
                @endcan

                @can('view-training')
                <li class="nav-item"><a class="nav-link {{ (request()->is('training/training*')) ? 'active' : ''  }}"
                        href="{{url('training/training')}}"><i class="icon-book"></i>Training</a>
                </li>
                @endcan





                @can('manage-gl-setup')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('gl_setup/*')) ? 'active' : ''  }}"><i
                            class="icon-wrench3"></i> <span>
                            GL SETUP</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">

                        @can('view-class_account')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('gl_setup/class_account*')) ? 'active' : ''  }}"
                                href="{{ url('gl_setup/class_account') }}">Class Account </a>
                        </li>
                        @endcan
                        @can('view-group_account')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('gl_setup/group_account*')) ? 'active' : ''  }}"
                                href="{{ url('gl_setup/group_account') }}">Group Account</a>
                        </li>
                        @endcan
                        @can('view-account_codes')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('gl_setup/account_codes*')) ? 'active' : ''  }}"
                                href="{{ url('gl_setup/account_codes') }}">Account Codes</a>
                        </li>
                        @endcan
                        @can('view-chart_of_account')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('gl_setup/chart_of_account*')) ? 'active' : ''  }}"
                                href="{{ url('gl_setup/chart_of_account') }}">Chart of Accounts
                            </a></li>
                        @endcan
                    </ul>
                </li>
                @endcan




                @can('manage-transaction')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('gl_setup/*')) ? 'active' : ''  }}"><i
                            class="icon-diamond"></i> <span>
                            Transactions</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">

                        @can('view-deposit')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('gl_setup/deposit*')) ? 'active' : ''  }}"
                                href="{{ url('gl_setup/deposit') }}">Deposit</a></li>
                        @endcan
                        @can('view-expenses')
                        <li class="nav-item "><a
                                class="nav-link {{ (request()->is('gl_setup/expenses*')) ? 'active' : ''  }}"
                                href="{{ url('gl_setup/expenses') }}">Payments</a></li>
                        @endcan
                        @can('view-transfer')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('gl_setup/transfer2*')) ? 'active' : ''  }}"
                                href="{{ url('gl_setup/transfer2') }}">Transfer</a></li>
                        @endcan
                        @can('view-expenses')
                        <li class="nav-item "><a
                                class="nav-link {{ (request()->is('gl_setup/account*')) ? 'active' : ''  }}"
                                href="{{ url('gl_setup/account') }}">Bank & Cash</a></li>
                        @endcan
                        @can('view-bank_statement')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('accounting/bank_statement*')) ? 'active' : ''  }}"
                                href="{{ url('accounting/bank_statement') }}">Bank
                                Statement</a>
                        </li>
                        @endcan
                        @can('view-bank_reconciliation')
                        <li class=" nav-item"><a
                                class="nav-link {{ (request()->is('accounting/bank_reconciliation*')) ? 'active' : ''  }}"
                                href="{{ url('accounting/bank_reconciliation') }}">Bank
                                Reconciliation</a></li>
                        @endcan
                        @can('view-reconciliation_report')
                        <li class="nav-item "><a
                                class="nav-link {{ (request()->is('accounting/reconciliation_report*')) ? 'active' : ''  }}"
                                href="{{ url('accounting/reconciliation_report') }}">Bank
                                Reconciliation Report</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can('manage-accounting')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('accounting/*')) ? 'active' : ''  }}"><i
                            class="icon-stats-growth"></i> <span>
                            Accounting</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                        @can('view-manual_entry')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('accounting/manual_entry*')) ? 'active' : ''  }}"
                                href="{{ url('accounting/manual_entry') }}">Journal
                                Entry</a></li>
                        @endcan
                        @can('view-journal')
                        <li class="nav-item "><a
                                class="nav-link {{ (request()->is('accounting/journal*')) ? 'active' : ''  }}"
                                href="{{ url('accounting/journal') }}">Journal Entry
                                Report</a>

                        </li>
                        @endcan
                        @can('view-ledger')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('accounting/ledger*')) ? 'active' : ''  }}"
                                href="{{ url('accounting/ledger') }}">Ledger</a></li>
                        @endcan
                        @can('view-trial_balance')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('financial_report/trial_balance*')) ? 'active' : ''  }}"
                                href="{{url('financial_report/trial_balance')}}">Trial
                                Balance </a>
                        </li>
                        @endcan
                        @can('view-trial_balance')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('financial_report/trial_balance_summary*')) ? 'active' : ''  }}"
                                href="{{url('financial_report/trial_balance_summary')}}">Trial
                                Balance Summary </a>
                        </li>
                        @endcan
                        @can('view-income_statement')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('financial_report/income_statement*')) ? 'active' : ''  }}"
                                href="{{url('financial_report/income_statement')}}">Income
                                Statement</a></li>
                        @endcan
                        @can('view-income_statement')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('financial_report/income_statement_summary*')) ? 'active' : ''  }}"
                                href="{{url('financial_report/income_statement_summary')}}">Income
                                Statement Summary</a></li>
                        @endcan
                        @can('view-balance_sheet')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('financial_report/balance_sheet*')) ? 'active' : ''  }}"
                                href="{{url('financial_report/balance_sheet')}}">Balance Sheet </a>
                        </li>
                        @endcan
                        @can('view-balance_sheet')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('financial_report/balance_sheet_summary*')) ? 'active' : ''  }}"
                                href="{{url('financial_report/balance_sheet_summary')}}">Balance
                                Sheet Summary </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @can('manage-cotton')
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('report/*')) ? 'active' : ''  }} active"><i
                            class="icon-copy"></i> <span>
                            Reports</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">

                        @can('view-stock-report')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('report/stock_report*')) ? 'active' : ''  }}"
                                href="{{url('report/stock_report')}}"> Stock

                                Report</a></li>
                        @endcan
                        @can('view-invoice-report')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('report/invoice_report*')) ? 'active' : ''  }}"
                                href="{{url('report/invoice_report')}}"> Invoice
                                Report</a></li>
                        @endcan
                        @can('view-center-report')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('report/center_report*')) ? 'active' : ''  }}"
                                href="{{url('report/center_report')}}"> Collection
                                Center Report</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('report/cotton_movement_report*')) ? 'active' : ''  }}"
                                href="{{url('report/cotton_movement_report')}}">
                                Cotton Movement Report</a></li>
                        @endcan
                        @can('view-levy-report')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('report/levy_report*')) ? 'active' : ''  }}"
                                href="{{url('report/levy_report')}}"> Levy Report</a>
                        </li>
                        @endcan
                        @can('view-levy-report')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('report/debtors_report*')) ? 'active' : ''  }}"
                                href="{{url('report/debtors_report')}}"> Debtors
                                Report</a></li>
                        @endcan
                        @can('view-center-report')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('report/general_report*')) ? 'active' : ''  }}"
                                href="{{url('report/general_report')}}"> Report By
                                District</a></li>
                        @endcan
                        @can('view-center-report')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('report/general_report2*')) ? 'active' : ''  }}"
                                href="{{url('report/general_report2')}}"> General
                                Report </a></li>
                        @endcan



                    </ul>
                </li>
                @endcan

                <li class="nav-item"><a href="{{url('chatify')}}"
                        class="nav-link {{ (request()->is('chatify*')) ? 'active' : ''  }}"><i
                            class="icon-envelop5"></i> <span>Chattings</span> </a></li>



                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ (request()->is('access_control/*')) ? 'active' : ''  }}"><i
                            class="icon-cog7"></i> <span>
                            {{__('permission.access_control')}}</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Layouts">

                        <li class=" nav-item"><a
                                class="nav-link {{ (request()->is('access_control/roleGroup*')) ? 'active' : ''  }}"
                                href="{{url('access_control/roles')}}">
                                {{__('permission.roles')}}</a>
                        </li>

                        @can('view-permission')
                        <li class=" nav-item "><a
                                class="nav-link {{ (request()->is('access_control/roleGroup*')) ? 'active' : ''  }}"
                                href="{{ url('access_control/permissions')}}">{{__('permission.permissions')}}</a>

                        </li>
                        @endcan
                        @can('view-user')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('access_control/system*')) ? 'active' : ''  }}"
                                href="{{ url('access_control/system')}}">{{__('permission.system_setings')}}</a>

                        </li>
                        @endcan

                        @can('view-user')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('access_control/departments*')) ? 'active' : ''  }}"
                                href="{{url('access_control/departments')}}">Departments
                            </a></li>
                        @endcan

                        @can('view-user')
                        <li class="nav-item"><a
                                class="nav-link {{ (request()->is('access_control/designations*')) ? 'active' : ''  }}"
                                href="{{url('access_control/designations')}}">Designations
                            </a></li>
                        @endcan

                        @can('view-user')
                        <li class=" nav-item "><a
                                class="nav-link {{ (request()->is('access_control/users*')) ? 'active' : ''  }}"
                                href="{{url('access_control/users')}}">{{__('permission.user')}}
                                Management</a></li>
                        @endcan


                    </ul>
                </li>

                <!-- /page kits -->

            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>