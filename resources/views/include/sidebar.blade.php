<nav id="sidebar">
    @include('include.logo')
    <ul class="list-unstyled menu-categories" id="accordionExample">
        <li class="menu">
            <a href="{{ url('/dashboard/dashboard1') }}" data-active="{{ is_active_route(['dashboard/*']) }}"
               data-toggle="collapse"
               aria-expanded="{{ is_active_route(['dashboard/*']) }}" class="dropdown-toggle">
                <div class="">
                    <i class="las la-home"></i>
                    <span>{{__('Dashboards')}}</span>
                </div>
                <div>
                    <i class="las la-angle-right sidemenu-right-icon"></i>
                </div>
            </a>
            @can('view-dashoard')
                <ul class="submenu list-unstyled collapse {{ show_class(['dashboard/*']) }}" id="dashboard"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['dashboard/dashboard1']) }}">
                        <a data-active="{{ is_active_route(['dashboard/dashboard1']) }}"
                           href="{{ url('/dashboard/dashboard1') }}"> {{__('Dashboard 1')}} </a>
                    </li>
                    <li class=" {{ active_class(['dashboard/dashboard2']) }}">
                        <a data-active="{{ is_active_route(['dashboard/dashboard2']) }}"
                           href="{{ url('/dashboard/dashboard2') }}"> {{__('Dashboard 2')}} </a>
                    </li>
                    <li class=" {{ active_class(['dashboard/dashboard3']) }}">
                        <a data-active="{{ is_active_route(['dashboard/dashboard3']) }}"
                           href="{{ url('/dashboard/dashboard3') }}"> {{__('Dashboard 3')}} </a>
                    </li>
                    <li class=" {{ active_class(['dashboard/dashboard4']) }}">
                        <a data-active="{{ is_active_route(['dashboard/dashboard4']) }}"
                           href="{{ url('/dashboard/dashboard4') }}"> {{__('Dashboard 4')}} </a>
                    </li>
                    <li class=" {{ active_class(['dashboard/dashboard5']) }}">
                        <a data-active="{{ is_active_route(['dashboard/dashboard5']) }}"
                           href="{{ url('/dashboard/dashboard5') }}"> {{__('Dashboard 5')}} </a>
                    </li>
                    <li class=" {{ active_class(['dashboard/dashboard-social']) }}">
                        <a data-active="{{ is_active_route(['dashboard/dashboard-social']) }}"
                           href="{{ url('/dashboard/dashboard-social') }}"> {{__('Dashboard Social')}} </a>
                    </li>
                </ul>
            @endcan
        </li>
        @can('view-dashoard')
            <li class="menu">
                <a href="#app" data-active="{{ is_active_route(['apps/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['apps/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-medapps"></i>
                        <span>{{__('Apps')}}</span>
                    </div>
                    <div>
                        <i class="las la-angle-right sidemenu-right-icon"></i>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['apps/*']) }}" id="app"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['apps/calendar']) }}">
                        <a data-active="{{ is_active_route(['apps/calendar']) }}" href="{{ url('/apps/calendar') }}">
                            {{__('Calendar')}} </a>
                    </li>
                    <li class=" {{ active_class(['apps/chat']) }}">
                        <a data-active="{{ is_active_route(['apps/chat']) }}"
                           href="{{ url('/apps/chat') }}"> {{__('Chat')}}
                        </a>
                    </li>
                    <li class=" {{ active_class(['apps/companies/*']) }}">
                        <a href="#pages-companies" data-active="{{ is_active_route(['apps/companies/*']) }}"
                           data-toggle="collapse" aria-expanded="{{ is_active_route(['apps/companies/*']) }}"
                           class="dropdown-toggle"> {{__('Companies')}} <i
                                class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu {{ show_class(['apps/companies/*']) }}"
                            id="pages-companies" data-parent="#pages">
                            <li>
                                <a data-active="{{ is_active_route(['apps/companies/list']) }}"
                                   href="{{ url('/apps/companies/lists') }}"> {{__('List')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/companies/company-details']) }}"
                                   href="{{ url('/apps/companies/company-details') }}"> {{__('Company Details')}} </a>
                            </li>
                        </ul>
                    </li>
                    <li class=" {{ active_class(['apps/contacts']) }}">
                        <a data-active="{{ is_active_route(['apps/contacts']) }}" href="{{ url('/apps/contacts') }}">
                            {{__('Contacts')}} </a>
                    </li>
                    <li class=" {{ active_class(['apps/ecommerce/*']) }}">
                        <a href="#pages-ecommerce" data-active="{{ is_active_route(['apps/ecommerce/*']) }}"
                           data-toggle="collapse" aria-expanded="{{ is_active_route(['apps/ecommerce/*']) }}"
                           class="dropdown-toggle"> {{__('Ecommerce')}} <i
                                class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu {{ show_class(['apps/ecommerce/*']) }}"
                            id="pages-ecommerce" data-parent="#pages">
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/dashboard']) }}"
                                   href="{{ url('/apps/ecommerce/dashboard') }}"> {{__('Dashboard')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/products']) }}"
                                   href="{{ url('/apps/ecommerce/products') }}"> {{__('Products')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/product-details']) }}"
                                   href="{{ url('/apps/ecommerce/product-details') }}"> {{__('Product Details')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/add-product']) }}"
                                   href="{{ url('/apps/ecommerce/add-product') }}"> {{__('Add Product')}}</a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/orders']) }}"
                                   href="{{ url('/apps/ecommerce/orders') }}"> {{__('Orders')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/order-details']) }}"
                                   href="{{ url('/apps/ecommerce/order-details') }}"> {{__('Order Details')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/customers']) }}"
                                   href="{{ url('/apps/ecommerce/customers') }}"> {{__('Customers')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/sellers']) }}"
                                   href="{{ url('/apps/ecommerce/sellers') }}"> {{__('Sellers')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/cart']) }}"
                                   href="{{ url('/apps/ecommerce/cart') }}"> {{__('Cart')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/ecommerce/checkout']) }}"
                                   href="{{ url('/apps/ecommerce/checkout') }}"> {{__('Checkout')}} </a>
                            </li>
                        </ul>
                    </li>
                    <li class=" {{ active_class(['apps/email/*']) }}">
                        <a href="#pages-email" data-active="{{ is_active_route(['apps/email/*']) }}"
                           data-toggle="collapse"
                           aria-expanded="{{ is_active_route(['apps/email/*']) }}"
                           class="dropdown-toggle"> {{__('Email')}}
                            <i class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu {{ show_class(['apps/email/*']) }}"
                            id="pages-email"
                            data-parent="#pages">
                            <li>
                                <a data-active="{{ is_active_route(['apps/email/inbox']) }}"
                                   href="{{ url('/apps/email/inbox') }}"> {{__('Inbox')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/email/details']) }}"
                                   href="{{ url('/apps/email/details') }}"> {{__('Email Details')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/email/compose']) }}"
                                   href="{{ url('/apps/email/compose') }}"> {{__('Compose Email')}} </a>
                            </li>
                        </ul>
                    </li>
                    <li class=" {{ active_class(['apps/file-manager']) }}">
                        <a data-active="{{ is_active_route(['apps/file-manager']) }}"
                           href="{{ url('/apps/file-manager') }}"> {{__('File Manager')}} </a>
                    </li>
                    <li class=" {{ active_class(['apps/invoice-list']) }}">
                        <a data-active="{{ is_active_route(['apps/invoice-list']) }}"
                           href="{{ url('/apps/invoice-list') }}"> {{__('Invoice List')}} </a>
                    </li>
                    <li class=" {{ active_class(['apps/notes/*']) }}">
                        <a href="#pages-notes" data-active="{{ is_active_route(['apps/notes/*']) }}"
                           data-toggle="collapse"
                           aria-expanded="{{ is_active_route(['apps/notes/*']) }}" class="dropdown-toggle">
                            {{ __('Notes')}} <i class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu {{ show_class(['apps/notes/*']) }}"
                            id="pages-notes"
                            data-parent="#pages">
                            <li>
                                <a data-active="{{ is_active_route(['apps/notes/list']) }}"
                                   href="{{ url('/apps/notes/list') }}"> {{__('List')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/notes/details']) }}"
                                   href="{{ url('/apps/notes/details') }}"> {{__('Notes Details')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/notes/create']) }}"
                                   href="{{ url('/apps/notes/create') }}"> {{__('Create Note')}} </a>
                            </li>
                        </ul>
                    </li>
                    <li class=" {{ active_class(['apps/social']) }}">
                        <a data-active="{{ is_active_route(['apps/social']) }}" href="{{ url('/apps/social') }}">
                            {{__('Social')}} </a>
                    </li>
                    <li class=" {{ active_class(['apps/task-list']) }}">
                        <a data-active="{{ is_active_route(['apps/task-list']) }}" href="{{ url('/apps/task-list') }}">
                            {{__('Task List')}} </a>
                    </li>
                    <li class=" {{ active_class(['apps/tickets/*']) }}">
                        <a href="#pages-tickets" data-active="{{ is_active_route(['apps/tickets/*']) }}"
                           data-toggle="collapse" aria-expanded="{{ is_active_route(['apps/tickets/*']) }}"
                           class="dropdown-toggle"> {{__('Tickets')}} <i
                                class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu {{ show_class(['apps/tickets/*']) }}"
                            id="pages-tickets" data-parent="#pages">
                            <li>
                                <a data-active="{{ is_active_route(['apps/tickets/list']) }}"
                                   href="{{ url('/apps/tickets/list') }}"> {{__('Ticket List')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['apps/tickets/details']) }}"
                                   href="{{ url('/apps/tickets/details') }}"> {{__('Ticket Details')}} </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="menu-title">{{__('Pages')}}</li>
        @endcan
        @can('view-staff-menu')
            <li class="menu">
                <a href="#authentication" data-active="{{ is_active_route(['authentications/*']) }}"
                   data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['authentications/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-lock"></i>
                        <span>{{__('Authorization')}}</span>
                    </div>
                    <div>
                        <!-- <i class="las la-angle-right sidemenu-right-icon"></i> -->
                        <span class="menu-badge badge-danger"> {{__('')}}</span>
                    </div>
                </a>

                <ul class="collapse submenu list-unstyled {{ show_class(['authentications/*']) }}" id="authentication"
                    data-parent="#accordionExample">
                    <li>
                        <a data-active="{{ is_active_route(['authorization/roles']) }}"
                           href="{{ url('/authorization/roles') }}"> {{__('Role')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['authorization/permissions']) }}"
                           href="{{ url('/authorization/permissions') }}"> {{__('Permission')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['authorization/departments']) }}"
                           href="{{ url('/authorization/departments') }}"> {{__('Departments')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['authorization/designations']) }}"
                           href="{{ url('/authorization/designations') }}"> {{__('Designations')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['authorization/users']) }}"
                           href="{{ url('/authorization/users') }}"> {{__('Manage Users')}}</a>
                    </li>

                    @can('view-other')
                        <li class=" {{ active_class(['authentications/style1/*']) }}">
                            <a href="#autholization" data-active="{{ is_active_route(['authentications/style1/*']) }}"
                               data-toggle="collapse" aria-expanded="{{ is_active_route(['authorization/*']) }}"
                               class="dropdown-toggle"> {{__('Role & Permission')}} <i
                                    class="las la-angle-right sidemenu-right-icon"></i> </a>
                            <ul class="collapse list-unstyled sub-submenu {{ show_class(['authentications/style1/*']) }}"
                                id="autholization" data-parent="#pages">

                                <li>
                                    <a data-active="{{ is_active_route(['authorization/roles']) }}"
                                       href="{{ url('/authorization/roles') }}"> {{__('Role')}}</a>
                                </li>

                                <li>
                                    <a data-active="{{ is_active_route(['authorization/permissions']) }}"
                                       href="{{ url('/authorization/permissions') }}"> {{__('Permission')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authorization/departments']) }}"
                                       href="{{ url('/authorization/departments') }}"> {{__('Departments')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authorization/designations']) }}"
                                       href="{{ url('/authorization/designations') }}"> {{__('Designation')}}</a>
                                </li>

                                <li>
                                    <a data-active="{{ is_active_route(['authorization/users']) }}"
                                       href="{{ url('/authorization/users') }}"> {{__('Manage Users')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style1/forgot-password']) }}"
                                       href="{{ url('/authentications/style1/forgot-password') }}"> {{__('Forget Password')}}
                                    </a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style1/confirm-email']) }}"
                                       href="{{ url('/authentications/style1/confirm-email') }}"> {{__('Confirm Email')}} </a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                    @can('view-style')
                        <li class=" {{ active_class(['authentications/style1/*']) }}">
                            <a href="#pages-auth-one" data-active="{{ is_active_route(['authentications/style1/*']) }}"
                               data-toggle="collapse"
                               aria-expanded="{{ is_active_route(['authentications/style1/*']) }}"
                               class="dropdown-toggle"> {{__('Style 1')}} <i
                                    class="las la-angle-right sidemenu-right-icon"></i> </a>
                            <ul class="collapse list-unstyled sub-submenu {{ show_class(['authentications/style1/*']) }}"
                                id="pages-auth-one" data-parent="#pages">
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style1/login']) }}"
                                       href="{{ url('/authentications/style1/login') }}"> {{__('Login')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style1/signup']) }}"
                                       href="{{ url('/authentications/style1/signup') }}"> {{__('Register')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style1/locked']) }}"
                                       href="{{ url('/authentications/style1/locked') }}"> {{__('Lock Screen')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style1/forgot-password']) }}"
                                       href="{{ url('/authentications/style1/forgot-password') }}"> {{__('Forget Password')}}
                                    </a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style1/confirm-email']) }}"
                                       href="{{ url('/authentications/style1/confirm-email') }}"> {{__('Confirm Email')}} </a>
                                </li>
                            </ul>
                        </li>
                        <li class=" {{ active_class(['authentications/style2/*']) }}">
                            <a href="#pages-auth-two" data-active="{{ is_active_route(['authentications/style2/*']) }}"
                               data-toggle="collapse"
                               aria-expanded="{{ is_active_route(['authentications/style2/*']) }}"
                               class="dropdown-toggle"> Style 2 <i class="las la-angle-right sidemenu-right-icon"></i>
                            </a>
                            <ul class="collapse list-unstyled sub-submenu {{ show_class(['authentications/style2/*']) }}"
                                id="pages-auth-two" data-parent="#pages">
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style2/login']) }}"
                                       href="{{ url('/authentications/style2/login') }}"> {{__('Login')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style2/signup']) }}"
                                       href="{{ url('/authentications/style2/signup') }}"> {{__('Register')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style2/locked']) }}"
                                       href="{{ url('/authentications/style2/locked') }}"> {{__('Lock Screen')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style2/forgot-password']) }}"
                                       href="{{ url('/authentications/style2/forgot-password') }}"> {{__('Forget Password')}}
                                    </a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style2/confirm-email']) }}"
                                       href="{{ url('/authentications/style2/confirm-email') }}"> {{__('Confirm Email')}} </a>
                                </li>
                            </ul>
                        </li>
                        <li class=" {{ active_class(['authentications/style3/*']) }}">
                            <a href="#pages-auth-three"
                               data-active="{{ is_active_route(['authentications/style3/*']) }}"
                               data-toggle="collapse"
                               aria-expanded="{{ is_active_route(['authentications/style3/*']) }}"
                               class="dropdown-toggle"> {{__('Style 3')}} <i
                                    class="las la-angle-right sidemenu-right-icon"></i> </a>
                            <ul class="collapse list-unstyled sub-submenu {{ show_class(['authentications/style3/*']) }}"
                                id="pages-auth-three" data-parent="#pages">
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style3/login']) }}"
                                       href="{{ url('/authentications/style3/login') }}"> {{__('Login')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style3/signup']) }}"
                                       href="{{ url('/authentications/style3/signup') }}"> {{__('Register')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style3/locked']) }}"
                                       href="{{ url('/authentications/style3/locked') }}"> {{__('Lock Screen')}}</a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style3/forgot-password']) }}"
                                       href="{{ url('/authentications/style3/forgot-password') }}"> {{__('Forget Password')}}
                                    </a>
                                </li>
                                <li>
                                    <a data-active="{{ is_active_route(['authentications/style3/confirm-email']) }}"
                                       href="{{ url('/authentications/style3/confirm-email') }}"> {{__('Confirm Email')}} </a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                </ul>
            </li>

            <li class="menu">
                <a href="#transaction" data-active="{{ is_active_route(['transaction/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['transaction/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-wpforms"></i><span>Transaction</span>
                    </div>

                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['transaction']) }}" id="transaction"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['deposit']) }}">
                        <a data-active="{{ is_active_route(['deposit']) }}" href="{{ url('deposit') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Deposit')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['expenses']) }}">
                        <a data-active="{{ is_active_route(['expenses']) }}" href="{{ url('expenses') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Expenses')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['accounting/bank_statement']) }}">
                        <a data-active="{{ is_active_route(['accounting/bank_statement']) }}"
                           href="{{ url('accounting/bank_statement') }}" aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Bank Statement')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['accounting/bank_reconciliation']) }}">
                        <a data-active="{{ is_active_route(['accounting/bank_reconciliation']) }}"
                           href="{{ url('accounting/bank_reconciliation') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Bank Reconciliation')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['accounting/reconciliation_report']) }}">
                        <a data-active="{{ is_active_route(['accounting/reconciliation_report']) }}"
                           href="{{ url('accounting/reconciliation_report') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Bank Reconciliation Report')}}</span>

                        </a>
                    </li>

                </ul>
            </li>

            <li class="menu">
                <a href="#cards" data-active="{{ is_active_route(['transaction/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['transaction/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-wpforms"></i><span>Manage Card </span>
                    </div>

                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['cards']) }}" id="cards"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['cards']) }}">
                        <a data-active="{{ is_active_route(['cards']) }}" href="{{ url('manage_cards') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Manage cards')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['card_deposit']) }}">
                        <a data-active="{{ is_active_route(['card_deposit']) }}" href="{{ url('card_deposit') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Deposit')}}</span>

                        </a>
                    </li>

                </ul>
            </li>

            <li class="menu">
                <a href="#manage_member" data-active="{{ is_active_route(['transaction/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['transaction/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-wpforms"></i><span>Manage Member </span>
                    </div>

                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['cards']) }}" id="manage_member"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['cards']) }}">
                        <a data-active="{{ is_active_route(['cards']) }}" href="{{ url('manage_member') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('New Member List')}}</span>

                        </a>
                    </li>
                    
                    
                    <li class=" {{ active_class(['card_deposit']) }}">
                        <a data-active="{{ is_active_route(['card_deposit']) }}" href="{{ url('manage_member') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('All Member List')}}</span>

                        </a>
                    </li>
 

                </ul>
            </li>

            <li class="menu">
                <a href="#accounting" data-active="{{ is_active_route(['inventory/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['inventory/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-wpforms"></i><span>Accounting</span>
                    </div>

                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['accounting/manual_entry/*']) }}"
                    id="accounting"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['class_account']) }}">
                        <a data-active="{{ is_active_route(['accounting/manual_entry/*']) }}"
                           href="{{ url('accounting/manual_entry') }}" aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Journal Entry')}}</span>

                        </a>
                    </li>

                    <li class="{{ active_class(['accounting/journal']) }}">
                        <a data-active="{{ is_active_route(['accounting/journal*']) }}"
                           href="{{ url('accounting/journal') }}" aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Journal Entry Report')}}</span>

                        </a>
                    </li>
                    <li class="{{ active_class(['accounting/ledger']) }}">
                        <a data-active="{{ is_active_route(['accounting/ledger*']) }}"
                           href="{{ url('accounting/ledger') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Ledger')}}</span>

                        </a>
                    </li>
                    <li class="{{ active_class(['financial_report/trial_balance']) }}">
                        <a data-active="{{ is_active_route(['financial_report/trial_balance*']) }}"
                           href="{{ url('financial_report/trial_balance') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Trial Balance ')}}</span>

                        </a>
                    </li>
                    <li class="{{ active_class(['financial_report/income_statement']) }}">
                        <a data-active="{{ is_active_route(['financial_report/income_statement*']) }}"
                           href="{{ url('financial_report/income_statement') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            {{__('Income Statement')}}

                        </a>
                    </li>
                    <li class="{{ active_class(['financial_report/balance_sheet']) }}">
                        <a data-active="{{ is_active_route(['financial_report/balance_sheet*']) }}"
                           href="{{ url('financial_report/balance_sheet') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Balance Sheet ')}}</span>

                        </a>
                    </li>


                </ul>
            </li>

            <li class="menu">
                <a href="#glSetup" data-active="{{ is_active_route(['inventory/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['inventory/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-wpforms"></i><span>GL SETUP</span>
                    </div>

                </a>

                <ul class="collapse submenu list-unstyled {{ show_class(['inventory/*']) }}" id="glSetup"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['class_account']) }}">
                        <a data-active="{{ is_active_route(['class_account/*']) }}" href="{{ url('class_account') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Class Account')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['group_account']) }}">
                        <a data-active="{{ is_active_route(['group_account/*']) }}" href="{{ url('group_account') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Group Account')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['account_codes']) }}">
                        <a data-active="{{ is_active_route(['account_codes/*']) }}" href="{{ url('account_codes') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Account Codes')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['chart_of_account']) }}">
                        <a data-active="{{ is_active_route(['chart_of_account/*']) }}"
                           href="{{ url('chart_of_account') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Chart of Accounts')}}</span>

                        </a>
                    </li>


                </ul>
            </li>


            <li class="menu">
                <a href="#inventory" data-active="{{ is_active_route(['inventory/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['inventory/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-truck"></i><span>Inventory</span>
                    </div>

                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['inventory/*']) }}" id="inventory"
                    data-parent="#accordionExample">

                    <li class=" {{ active_class(['location']) }}">
                        <a data-active="{{ is_active_route(['location/*']) }}" href="{{ url('location') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Location')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['inventory']) }}">
                        <a data-active="{{ is_active_route(['inventory/*']) }}" href="{{ url('inventory') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Inventory Items')}}</span>

                        </a>
                    </li>

                    <li class=" {{ active_class(['fieldstaff']) }}">
                        <a data-active="{{ is_active_route(['fieldstaff/*']) }}" href="{{ url('fieldstaff') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Field Staff')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['purchase_inventory']) }}">
                        <a data-active="{{ is_active_route(['purchase_inventory/*']) }}"
                           href="{{ url('purchase_inventory') }}" aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Purchase Inventory')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['inventory_list']) }}">
                        <a data-active="{{ is_active_route(['inventory_list/*']) }}" href="{{ url('inventory_list') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Inventory List')}}</span>

                        </a>
                    </li>

                    <li class=" {{ active_class(['good_issue']) }}">
                        <a data-active="{{ is_active_route(['good_issue/*']) }}" href="{{ url('good_issue') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Good Issue')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['good_return']) }}">
                        <a data-active="{{ is_active_route(['good_return/*']) }}" href="{{ url('good_return') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Good Return')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['good_movement']) }}">
                        <a data-active="{{ is_active_route(['good_movement/*']) }}" href="{{ url('good_movement') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Good Movement')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['good_reallocation']) }}">
                        <a data-active="{{ is_active_route(['good_reallocation/*']) }}"
                           href="{{ url('good_reallocation') }}" aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Good Reallocation')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['good_disposal']) }}">
                        <a data-active="{{ is_active_route(['good_disposal/*']) }}" href="{{ url('good_disposal') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Good Disposal')}}</span>

                        </a>
                    </li>

                </ul>
            </li>

            <li class="menu">
                <a href="#payroll" data-active="{{ is_active_route(['payroll/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['payroll/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-wallet"></i><span>Payroll</span>
                    </div>

                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['payroll']) }}" id="payroll"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['payroll/salary_template']) }}">
                        <a data-active="{{ is_active_route(['payroll/salary_template']) }}"
                           href="{{ url('payroll/salary_template') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Salary Template')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/manage_salary']) }}">
                        <a data-active="{{ is_active_route(['payroll/manage_salary']) }}"
                           href="{{ url('payroll/manage_salary') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Manage Salary')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/employee_salary_list']) }}">
                        <a data-active="{{ is_active_route(['apayroll/employee_salary_list']) }}"
                           href="{{ url('payroll/employee_salary_list') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Employee Salary List')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/make_payment']) }}">
                        <a data-active="{{ is_active_route(['payroll/make_payment']) }}"
                           href="{{ url('payroll/make_payment') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Make Payment')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/generate_payslip']) }}">
                        <a data-active="{{ is_active_route(['payroll/generate_payslip']) }}"
                           href="{{ url('payroll/generate_payslip') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Generate Payslip')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/payroll_summary']) }}">
                        <a data-active="{{ is_active_route(['payroll/payroll_summary']) }}"
                           href="{{ url('payroll/payroll_summary') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Payroll Summary')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/advance_salary']) }}">
                        <a data-active="{{ is_active_route(['payroll/advance_salary']) }}"
                           href="{{ url('payroll/advance_salary') }}" aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Advance Salary')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/employee_loan']) }}">
                        <a data-active="{{ is_active_route(['payroll/employee_loan']) }}"
                           href="{{ url('payroll/employee_loan') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Employee Loan')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/overtime']) }}">
                        <a data-active="{{ is_active_route(['payroll/overtime']) }}"
                           href="{{ url('payroll/overtime') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Overtime')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/nssf']) }}">
                        <a data-active="{{ is_active_route(['payroll/nssf']) }}" href="{{ url('payroll/nssf') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Social Security -NSSF')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/tax']) }}">
                        <a data-active="{{ is_active_route(['payroll/tax']) }}"
                           href="{{ url('payroll/tax') }}" aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Tax ')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/nhif']) }}">
                        <a data-active="{{ is_active_route(['payroll/nhif']) }}"
                           href="{{ url('payroll/nhif') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('Health Contribution')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['payroll/wcf']) }}">
                        <a data-active="{{ is_active_route(['payroll/wcf']) }}"
                           href="{{ url('payroll/wcf') }}" aria-expanded="false"
                           class="dropdown-toggle">

                            <span> {{__('WCF Contribution')}}</span>

                        </a>
                    </li>
                </ul>
            </li>



            <li class="menu {{ active_class(['leave']) }}">
                <a data-active="{{ is_active_route(['leave/*']) }}" href="{{ url('leave') }}" aria-expanded="false"
                   class="dropdown-toggle">
                    <div class="">
                        <i class="las la-plane"></i>
                        <span> {{__('Leave Management')}}</span>
                    </div>
                </a>
            </li>

            <li class="menu {{ active_class(['training']) }}">
                <a data-active="{{ is_active_route(['training/*']) }}" href="{{ url('training') }}"
                   aria-expanded="false"
                   class="dropdown-toggle">
                    <div class="">
                        <i class="las la-suitcase"></i>
                        <span> {{__('Training')}}</span>
                    </div>
                </a>
            </li>


            <li class="menu {{ active_class(['facility']) }}">
                <a data-active="{{ is_active_route(['facility/*']) }}" href="{{ url('facility') }}"
                   aria-expanded="false"
                   class="dropdown-toggle">
                    <div class="">
                        <i class="las la-sitemap"></i>
                        <span> {{__('Facilities')}}</span>
                    </div>
                </a>
            </li>

            <li class="menu">
                <a href="#membership" data-active="{{ is_active_route(['transaction/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['transaction/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-wpforms"></i><span>Manage Member </span>
                    </div>

                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['membership']) }}" id="membership"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['membership']) }}">
                        <a data-active="{{ is_active_route(['membership']) }}" href="{{ url('membership/member') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Register')}}</span>

                        </a>
                    </li>
                <!--
                <li class=" {{ active_class(['card_deposit']) }}">
                    <a data-active="{{ is_active_route(['card_deposit']) }}" href="{{ url('card_deposit') }}"
                        aria-expanded="false" class="dropdown-toggle">

                        <span> {{__('Deposit')}}</span>

                    </a>
                </li>

-->

                </ul>
            </li>
            <li class="menu {{ active_class(['Visitors']) }}">
                <a data-active="{{ is_active_route(['visitors/*']) }}" href="{{ url('visitors') }}"
                   aria-expanded="false"
                   class="dropdown-toggle">
                    <div class="">
                        <i class="las la-user"></i>
                        <span> {{__('Visitors')}}</span>
                    </div>
                </a>
            </li>
            <li class="menu">
                <a href="#restaurant" data-active="{{ is_active_route(['restaurant/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['restaurant/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="la la-store la-lw"></i><span>Restaurant</span>
                    </div>

                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['restaurant']) }}" id="restaurant"
                    data-parent="#accordionExample">

                    <li class=" {{ active_class(['restaurant/restaurants']) }}">
                        <a data-active="{{ is_active_route(['restaurant/restaurants']) }}"
                           href="{{ url('restaurant/restaurants') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Restaurants List')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['restaurant/tables']) }}">
                        <a data-active="{{ is_active_route(['restaurant/tables']) }}"
                           href="{{ url('restaurant/tables') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Tables List')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['menu-items']) }}">
                        <a data-active="{{ is_active_route(['menu-items']) }}" href="{{ url('restaurant/menu-items') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Menu items')}}</span>

                        </a>
                    </li>
                    <li class=" {{ active_class(['orders']) }}">
                        <a data-active="{{ is_active_route(['orders']) }}" href="{{ url('restaurant/orders') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Make Order')}}</span>

                        </a>
                    </li>
                <!--
                <li class=" {{ active_class(['restaurant']) }}">
                    <a data-active="{{ is_active_route(['foodcategory']) }}" href="{{ url('restaurant/category') }}"
                        aria-expanded="false" class="dropdown-toggle">

                        <span> {{__('Food Category')}}</span>

                    </a>
                </li>
                <li class=" {{ active_class(['restaurant']) }}">
                    <a data-active="{{ is_active_route(['foodcuisine']) }}" href="{{ url('restaurant/cuisine') }}"
                        aria-expanded="false" class="dropdown-toggle">

                        <span> {{__('Food Cuisine')}}</span>

                    </a>
                </li>

                <li class=" {{ active_class(['restauranthome']) }}">
                    <a data-active="{{ is_active_route(['restauranthome']) }}" href="{{ url('restaurant/restauranthome') }}"
                        aria-expanded="false" class="dropdown-toggle">

                        <span> {{__('Reservation Report')}}</span>

                    </a>
                </li>
                 <li class=" {{ active_class(['restaurant/dashboard']) }}">
                    <a data-active="{{ is_active_route(['restaurant/dashboard']) }}" href="{{ url('restaurant/dashboard') }}"
                        aria-expanded="false" class="dropdown-toggle">

                        <span> {{__('Restaurant Dashbord')}}</span>

                    </a>
                </li>
            -->
                </ul>
            </li>

            <li class="menu {{ active_class(['manage_bar']) }}">
                <a data-active="{{ is_active_route(['manage_bar/*']) }}" href="{{ url('manage_bar') }}"
                   aria-expanded="false"
                   class="dropdown-toggle">
                    <div class="">
                        <i class="la la-store la-lw"></i><span>Manage Bar</span>
                    </div>
                </a>
            </li>

            <li class="menu {{ active_class(['setting']) }}">
                <a data-active="{{ is_active_route(['setting/*']) }}" href="{{ url('setting') }}" aria-expanded="false"
                   class="dropdown-toggle">
                    <div class="">
                        <i class="las la-cog"></i>
                        <span> {{__('Settings')}}</span>
                    </div>
                </a>
            </li>
        @endcan
        @can('view-visitor-menu')
            <li class="menu">
                <a href="#cards" data-active="{{ is_active_route(['transaction/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['transaction/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-wpforms"></i><span>manage</span>
                    </div>

                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['cards']) }}" id="cards"
                    data-parent="#accordionExample">

                    <li class=" {{ active_class(['card_deposit']) }}">
                        <a data-active="{{ is_active_route(['card_deposit']) }}" href="{{ url('card_deposit') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Deposit')}}</span>

                        </a>
                    </li>

                </ul>
            </li>
        @endcan
        @can('view-member-menu')
            <li class="menu">
                <a href="#cards" data-active="{{ is_active_route(['transaction/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['transaction/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-wpforms"></i><span>manage</span>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['cards']) }}" id="cards"
                    data-parent="#accordionExample">

                    <li class=" {{ active_class(['member_card_deposit']) }}">
                        <a data-active="{{ is_active_route(['member_card_deposit']) }}"
                           href="{{ url('member_card_deposit') }}"
                           aria-expanded="false" class="dropdown-toggle">

                            <span> {{__('Deposit')}} </span>

                        </a>
                    </li>

                </ul>
            </li>
        @endcan

        @can('view-other-menu')
            <li class="menu">
                <a href="#pages" data-active="{{ is_active_route(['pages/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['pages/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-file"></i>
                        <span> {{__('Other Pages')}}</span>
                    </div>
                    <div>
                        <i class="las la-angle-right sidemenu-right-icon"></i>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['pages/*']) }}" id="pages"
                    data-parent="#accordionExample">
                    <li>
                        <a data-active="{{ is_active_route(['pages/coming-soon']) }}"
                           href="{{ url('/pages/coming-soon') }}"> {{__('Coming Soon')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/coming-soon2']) }}"
                           href="{{ url('/pages/coming-soon2') }}"> {{__('Coming Soon 2')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/contact-us']) }}"
                           href="{{ url('/pages/contact-us') }}">
                            {{__('Contact Form')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/contact-us2']) }}"
                           href="{{ url('/pages/contact-us2') }}"> {{__('Contact Form 2')}}</a>
                    </li>
                    <li class=" {{ active_class(['pages/error/*']) }}">
                        <a href="#pages-error" data-active="{{ is_active_route(['pages/error/*']) }}"
                           data-toggle="collapse"
                           aria-expanded="{{ is_active_route(['pages/error/*']) }}" class="dropdown-toggle">
                            {{__('Error')}} <i class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu {{ show_class(['pages/error/*']) }}"
                            id="pages-error"
                            data-parent="#pages">
                            <li>
                                <a data-active="{{ is_active_route(['pages/error/error404']) }}"
                                   href="{{ url('/pages/error/error404') }}"> 404 </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['pages/error/error500']) }}"
                                   href="{{ url('/pages/error/error500') }}"> 500 </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['pages/error/error503']) }}"
                                   href="{{ url('/pages/error/error503') }}"> 503 </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['pages/error/maintenance']) }}"
                                   href="{{ url('/pages/error/maintenance') }}"> {{__('Maintenance')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['pages/error/error404-two']) }}"
                                   href="{{ url('/pages/error/error404-two') }}"> 404 Two</a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['pages/error/error500-two']) }}"
                                   href="{{ url('/pages/error/error500-two') }}"> 500 Two </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['pages/error/error503-two']) }}"
                                   href="{{ url('/pages/error/error503-two') }}"> {{__('')}} 503 Two </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['pages/error/maintenance-two']) }}"
                                   href="{{ url('/pages/error/maintenance-two') }}"> {{__('Maintenance Two')}} </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/faq']) }}"
                           href="{{ url('/pages/faq') }}"> {{__('FAQ')}}
                        </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/faq2']) }}" href="{{ url('/pages/faq2') }}">
                            {{__('FAQ 2')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/faq3']) }}" href="{{ url('/pages/faq3') }}">
                            {{__('FAQ 3')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/helpdesk']) }}" href="{{ url('/pages/helpdesk') }}">
                            {{__('Helpdesk')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/notifications']) }}"
                           href="{{ url('/pages/notifications') }}"> {{__('Notifications')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/pricing']) }}" href="{{ url('/pages/pricing') }}">
                            {{__('Pricing')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/pricing2']) }}" href="{{ url('/pages/pricing2') }}">
                            {{__('Pricing 2')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/privacy-policy']) }}"
                           href="{{ url('/pages/privacy-policy') }}"> {{__('Privacy Policy')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/profile']) }}" href="{{ url('/pages/profile') }}">
                            {{__('Profile')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/profile-edit']) }}"
                           href="{{ url('/pages/profile-edit') }}"> {{__('Profile Edit')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/search-result']) }}"
                           href="{{ url('/pages/search-result') }}"> {{__('Search Result')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/search-result2']) }}"
                           href="{{ url('/pages/search-result2') }}"> {{__('Search Result 2')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/sitemap']) }}" href="{{ url('/pages/sitemap') }}">
                            {{__('Sitemap')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['pages/timeline']) }}" href="{{ url('/pages/timeline') }}">
                            {{__('Timeline')}} </a>
                    </li>
                </ul>
            </li>
            <li class="menu-title"> {{__('Components')}}</li>
            <li class="menu">
                <a href="#components" data-active="{{ is_active_route(['basic-ui/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['basic-ui/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-drafting-compass"></i>
                        <span>{{__('Basic UI')}}</span>
                    </div>
                    <div>
                        <i class="las la-angle-right sidemenu-right-icon"></i>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['basic-ui/*']) }}" id="components"
                    data-parent="#accordionExample">
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/accordions']) }}"
                           href="{{ url('/basic-ui/accordions') }}"> {{__('Accordions')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/animation']) }}"
                           href="{{ url('/basic-ui/animation') }}"> {{__('Animation')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/cards']) }}" href="{{ url('/basic-ui/cards') }}">
                            {{__('Bootstrap Cards')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/carousel']) }}"
                           href="{{ url('/basic-ui/carousel') }}"> {{__('Carousel')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/countdown']) }}"
                           href="{{ url('/basic-ui/countdown') }}"> {{__('Countdown')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/counter']) }}"
                           href="{{ url('/basic-ui/counter') }}">
                            {{__('Counter')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/dragitems']) }}"
                           href="{{ url('/basic-ui/dragitems') }}"> {{__('Drag Items')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/lightbox']) }}"
                           href="{{ url('/basic-ui/lightbox') }}"> {{__('Lightbox')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/lightbox-sideopen']) }}"
                           href="{{ url('/basic-ui/lightbox-sideopen') }}"> {{__('Lightbox Side Open')}}</a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/list-groups']) }}"
                           href="{{ url('/basic-ui/list-groups') }}"> {{__('List Group')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/media-object']) }}"
                           href="{{ url('/basic-ui/media-object') }}"> {{__('Media Object')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/modals']) }}"
                           href="{{ url('/basic-ui/modals') }}">
                            {{__('Modals')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/notifications']) }}"
                           href="{{ url('/basic-ui/notifications') }}"> {{__('Notifications')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/scrollspy']) }}"
                           href="{{ url('/basic-ui/scrollspy') }}"> {{__('Scroll Spy')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/session-timeout']) }}"
                           href="{{ url('/basic-ui/session-timeout') }}"> {{__('Session Timeout')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/sweet-alerts']) }}"
                           href="{{ url('/basic-ui/sweet-alerts') }}"> {{__('Sweet Alerts')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/tabs']) }}" href="{{ url('/basic-ui/tabs') }}">
                            {{__('Tabs')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['basic-ui/tour-tutorial']) }}"
                           href="{{ url('/basic-ui/tour-tutorial') }}"> {{__('Tour Tutorial')}} </a>
                    </li>
                </ul>
            </li>
            <li class="menu">
                <a href="#elements" data-active="{{ is_active_route(['ui-elements/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['ui-elements/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-elementor"></i>
                        <span> UI Elements </span>
                    </div>
                    <div>
                        <i class="las la-angle-right sidemenu-right-icon"></i>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['ui-elements/*']) }}" id="elements"
                    data-parent="#accordionExample">
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/alerts']) }}"
                           href="{{ url('/ui-elements/alerts') }}"> {{__('Alerts')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/avatar']) }}"
                           href="{{ url('/ui-elements/avatar') }}"> {{__('Avatar')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/badges']) }}"
                           href="{{ url('/ui-elements/badges') }}"> {{__('Badges')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/breadcrumbs']) }}"
                           href="{{ url('/ui-elements/breadcrumbs') }}"> {{ __('Breadcrumbs')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/buttons']) }}"
                           href="{{ url('/ui-elements/buttons') }}"> {{ __('Buttons')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/colors']) }}"
                           href="{{ url('/ui-elements/colors') }}"> {{ __('Colors')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/dropdown']) }}"
                           href="{{ url('/ui-elements/dropdown') }}"> {{ __('Dropdown')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/grid']) }}"
                           href="{{ url('/ui-elements/grid') }}">
                            {{ __('Grid')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/jumbotron']) }}"
                           href="{{ url('/ui-elements/jumbotron') }}"> {{ __('Jumbotron')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/list-group']) }}"
                           href="{{ url('/ui-elements/list-group') }}"> {{ __('List Group')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/loading-spinners']) }}"
                           href="{{ url('/ui-elements/loading-spinners') }}"> {{ __('Loading Spinners')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/paging']) }}"
                           href="{{ url('/ui-elements/paging') }}"> {{ __('Paging') }} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/popovers']) }}"
                           href="{{ url('/ui-elements/popovers') }}"> {{ __('Popovers')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/progress-bar']) }}"
                           href="{{ url('/ui-elements/progress-bar') }}"> {{ __('Progress Bar')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/ribbons']) }}"
                           href="{{ url('/ui-elements/ribbons') }}"> {{ __('Ribbons')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/tooltips']) }}"
                           href="{{ url('/ui-elements/tooltips') }}"> {{ __('Tooltips')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/typography']) }}"
                           href="{{ url('/ui-elements/typography') }}"> {{ __('Typography')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['ui-elements/video']) }}"
                           href="{{ url('/ui-elements/video') }}"> {{ __('Video')}} </a>
                    </li>
                </ul>
            </li>
            <li class="menu {{ active_class(['widgets']) }}">
                <a data-active="{{ is_active_route(['widgets']) }}" href="{{ url('/widgets') }}" aria-expanded="false"
                   class="dropdown-toggle">
                    <div class="">
                        <i class="las la-desktop"></i>
                        <span> {{__('Widgets')}}</span>
                    </div>
                </a>
            </li>
            <li class="menu {{ active_class(['tables']) }}">
                <a data-active="{{ is_active_route(['tables']) }}" href="{{ url('/tables') }}" aria-expanded="false"
                   class="dropdown-toggle">
                    <div class="">
                        <i class="las la-border-all"></i>
                        <span> {{__('Tables')}}</span>
                    </div>
                </a>
            </li>
            <li class="menu {{ active_class(['data-tables']) }}">
                <a data-active="{{ is_active_route(['data-tables']) }}" href="{{ url('/data-tables') }}"
                   aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-table"></i>
                        <span> {{__('Datatables')}}</span>
                    </div>
                </a>
            </li>
            <li class="menu">
                <a href="#forms" data-active="{{ is_active_route(['forms/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['forms/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="lab la-wpforms"></i>
                        <span> Forms</span>
                    </div>
                    <div>
                        <i class="las la-angle-right sidemenu-right-icon"></i>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['forms/*']) }}" id="forms"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['forms/controls/*']) }}">
                        <a href="#form-control" data-active="{{ is_active_route(['forms/controls/*']) }}"
                           data-toggle="collapse" aria-expanded="{{ is_active_route(['forms/controls/*']) }}"
                           class="dropdown-toggle"> {{__('Controls')}} <i
                                class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu {{ show_class(['forms/controls/*']) }}"
                            id="form-control" data-parent="#pages">
                            <li>
                                <a data-active="{{ is_active_route(['forms/controls/base-inputs']) }}"
                                   href="{{ url('/forms/controls/base-inputs') }}"> {{__('Base Input')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/controls/input-groups']) }}"
                                   href="{{ url('/forms/controls/input-groups') }}"> {{__('Input Groups')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/controls/checkbox']) }}"
                                   href="{{ url('/forms/controls/checkbox') }}"> {{__('Checkbox')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/controls/radio']) }}"
                                   href="{{ url('/forms/controls/radio') }}"> {{__('Radio')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/controls/switch']) }}"
                                   href="{{ url('/forms/controls/switch') }}"> {{__('Switch')}} </a>
                            </li>
                        </ul>
                    </li>
                    <li class=" {{ active_class(['forms/widgets/*']) }}">
                        <a href="#form-widget" data-active="{{ is_active_route(['forms/widgets/*']) }}"
                           data-toggle="collapse" aria-expanded="{{ is_active_route(['forms/widgets/*']) }}"
                           class="dropdown-toggle"> {{__('Widgets')}} <i
                                class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu {{ show_class(['forms/widgets/*']) }}"
                            id="form-widget" data-parent="#pages">
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/picker']) }}"
                                   href="{{ url('/forms/widgets/picker') }}"> {{__('Picker')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/tagify']) }}"
                                   href="{{ url('/forms/widgets/tagify') }}"> {{__('Tagify')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/touch-spin']) }}"
                                   href="{{ url('/forms/widgets/touch-spin') }}"> {{__('Touch Spin')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/maxlength']) }}"
                                   href="{{ url('/forms/widgets/maxlength') }}"> {{__('Max Length')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/switch']) }}"
                                   href="{{ url('/forms/widgets/switch') }}"> {{__('Switch')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/select-splitter']) }}"
                                   href="{{ url('/forms/widgets/select-splitter') }}"> {{__('Select Splitter')}}</a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/bootstrap-select']) }}"
                                   href="{{ url('/forms/widgets/bootstrap-select') }}"> {{__('Bootstrap Select')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/select2']) }}"
                                   href="{{ url('/forms/widgets/select2') }}"> {{__('Select 2')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/input-masks']) }}"
                                   href="{{ url('/forms/widgets/input-masks') }}"> {{__('Input Masks')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/autogrow']) }}"
                                   href="{{ url('/forms/widgets/autogrow') }}"> {{__('Autogrow')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/range-slider']) }}"
                                   href="{{ url('/forms/widgets/range-slider') }}"> {{__('Range Slider')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/clipboard']) }}"
                                   href="{{ url('/forms/widgets/clipboard') }}"> {{__('Clipboard')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/typeahead']) }}"
                                   href="{{ url('/forms/widgets/typeahead') }}"> {{__('Typeahead')}} </a>
                            </li>
                            <li>
                                <a data-active="{{ is_active_route(['forms/widgets/captcha']) }}"
                                   href="{{ url('/forms/widgets/captcha') }}"> {{__('Captcha')}} </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['forms/validation']) }}"
                           href="{{ url('/forms/validation') }}">
                            {{__('Validations')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['forms/layouts']) }}" href="{{ url('/forms/layouts') }}">
                            {{__('Layouts')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['forms/text-editor']) }}"
                           href="{{ url('/forms/text-editor') }}"> {{__('Text Editor')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['forms/file-upload']) }}"
                           href="{{ url('/forms/file-upload') }}"> {{__('File Upload')}} </a>
                    </li>
                    <li>
                        <a data-active="{{ is_active_route(['forms/multiple-step']) }}"
                           href="{{ url('/forms/multiple-step') }}"> {{__('Multiple Step')}} </a>
                    </li>
                </ul>
            </li>
            <li class="menu">
                <a href="#maps" data-active="{{ is_active_route(['maps/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['maps/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-globe-americas"></i>
                        <span> {{__('Maps')}}</span>
                    </div>
                    <div>
                        <i class="las la-angle-right sidemenu-right-icon"></i>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['maps/*']) }}" id="maps"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['maps/leaflet-map']) }}">
                        <a data-active="{{ is_active_route(['maps/leaflet-map']) }}"
                           href="{{ url('/maps/leaflet-map') }}">
                            {{__('Leaflet Map')}} </a>
                    </li>
                    <li class=" {{ active_class(['maps/vector-map']) }}">
                        <a data-active="{{ is_active_route(['maps/vector-map']) }}"
                           href="{{ url('/maps/vector-map') }}">
                            {{__('Vector Map')}} </a>
                    </li>
                </ul>
            </li>
            <li class="menu">
                <a href="#charts" data-active="{{ is_active_route(['charts/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['charts/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-chart-pie"></i>
                        <span> {{__('Charts')}}</span>
                    </div>
                    <div>
                        <i class="las la-angle-right sidemenu-right-icon"></i>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['charts/*']) }}" id="charts"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['charts/apex-chart']) }}">
                        <a data-active="{{ is_active_route(['charts/apex-chart']) }}"
                           href="{{ url('/charts/apex-chart') }}"> {{__('Apex Chart')}} </a>
                    </li>
                    <li class=" {{ active_class(['charts/chartlist-chart']) }}">
                        <a data-active="{{ is_active_route(['charts/chartlist-chart']) }}"
                           href="{{ url('/charts/chartlist-chart') }}"> {{__('Chartlist Charts')}} </a>
                    </li>
                    <li class=" {{ active_class(['charts/chartjs']) }}">
                        <a data-active="{{ is_active_route(['charts/chartjs']) }}" href="{{ url('/charts/chartjs') }}">
                            {{__('ChartJS')}} </a>
                    </li>
                    <li class=" {{ active_class(['charts/morris-chart']) }}">
                        <a data-active="{{ is_active_route(['charts/morris-chart']) }}"
                           href="{{ url('/charts/morris-chart') }}"> {{__('Morris Charts')}} </a>
                    </li>
                </ul>
            </li>
            <li class="menu">
                <a href="#starter-kit" data-active="{{ is_active_route(['starter/*']) }}" data-toggle="collapse"
                   aria-expanded="{{ is_active_route(['starter/*']) }}" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-copy"></i>
                        <span> {{__('Starter Kit')}}</span>
                    </div>
                    <div>
                        <i class="las la-angle-right sidemenu-right-icon"></i>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ show_class(['starter/*']) }}" id="starter-kit"
                    data-parent="#accordionExample">
                    <li class=" {{ active_class(['starter/blank-page']) }}">
                        <a data-active="{{ is_active_route(['starter/blank-page']) }}"
                           href="{{ url('/starter/blank-page') }}"> {{__('Blank Page')}} </a>
                    </li>
                    <li class=" {{ active_class(['starter/breadcrumbs']) }}">
                        <a data-active="{{ is_active_route(['starter/breadcrumbs']) }}"
                           href="{{ url('/starter/breadcrumbs') }}"> {{__('Breadcrumbs')}} </a>
                    </li>
                </ul>
            </li>
            <li class="menu">
                <a href="#multi-level" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-sitemap"></i>
                        <span> {{__('Multi Level')}}</span>
                    </div>
                    <div>
                        <i class="las la-angle-right sidemenu-right-icon"></i>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled" id="multi-level" data-parent="#accordionExample">
                    <li>
                        <a href="#level-two" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            {{__('Level 2')}} <i class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu" id="level-two" data-parent="#pages">
                            <li>
                                <a href="javascript:void(0)"> {{__('Link 1')}} </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"> {{__('Link 2')}} </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#level-three" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            {{__('Level 3')}} <i class="las la-angle-right sidemenu-right-icon"></i> </a>
                        <ul class="collapse list-unstyled sub-submenu" id="level-three" data-parent="#pages">
                            <li>
                                <a href="javascript:void(0)"> {{__('Link 1')}}</a>
                            </li>
                            <li>
                                <a href="#level-four" data-toggle="collapse" aria-expanded="false"
                                   class="dropdown-toggle">
                                    {{__('Link 2')}} <i class="las la-angle-right sidemenu-right-icon"></i> </a>
                                <ul class="collapse list-unstyled sub-sub-submenu" id="level-four" data-parent="#pages">
                                    <li>
                                        <a href="javascript:void(0)"> {{__('Link 1')}}</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)"> {{__('Link 2')}} </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="menu-title"> {{__('Others')}}</li>
            <li class="menu">
                <a href="http://neptuneweb.xyz" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <i class="las la-file-code"></i>
                        <span> {{__('Documentation')}}</span>
                    </div>
                </a>
            </li>
        @endcan
    </ul>
</nav>
