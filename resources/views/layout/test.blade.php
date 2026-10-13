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

                       <li class="nav-item"> <a href="#" class="nav-link {{ (request()->is('apps/companies/*')) ? 'active' : ''  }} "><i
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