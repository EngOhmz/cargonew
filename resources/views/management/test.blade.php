@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
{!! Html::style('plugins/flatpickr/flatpickr.css') !!}
{!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
{!! Html::style('assets/css/elements/tooltip.css') !!}

<script src="{{asset('global_assets/js/plugins/forms/inputs/inputmask.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/selects/bootstrap_multiselect.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/uploaders/bs_custom_file_input.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/extensions/jquery_ui/core.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/tags/tagsinput.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/tags/tokenfield.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/touchspin.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/maxlength.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/formatter.min.js')}}"></script>
<script src="{{asset('global_assets/js/demo_pages/form_floating_labels.js')}}"></script>


<script src="{{asset('global_assets/js/plugins/tables/datatables/datatables.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/tables/datatables/extensions/buttons.min.js')}}"></script>

	<!-- <script src="assets/js/app.js"></script> -->
	<script src="{{asset('global_assets/js/demo_pages/datatables_extension_buttons_html5.js')}}"></script>
@endpush
@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">



         <!-- Basic initialization -->
         <div class="card">

<div class="card-header">
    <h5 class="card-title">Basic initialization rs</h5>
</div>

<div class="card-body">
    The HTML5 export buttons plug-in for Buttons provides four export buttons: <code>copyHtml5</code> - copy to clipboard; <code>csvHtml5</code> - save to CSV file; <code>excelHtml5</code> - save to XLSX file (requires JSZip); <code>pdfHtml5</code> - save to PDF file (requires PDFMake). This example demonstrates these four button types with their default options. Please note that these button types may also use a Flash fallback for older browsers (IE9-).
</div>

Skip to content
DEV Community 👩‍💻👨‍💻
Search...

Log in
Create account

9
Like
6
Jump to Comments
12
Save

Kingsconsult
Kingsconsult
Posted on Sep 21, 2020 • Updated on Dec 25, 2020

How to create modal in Laravel 8 and Laravel 6/7 with AJax
#
laravel
#
modal
#
ajax
#
jquery
Bootstrap Modal (2 Part Series)
1
How to create modal in Laravel 8 and Laravel 6/7 with AJax
2
How to implement Delete Confirmation in Laravel 8, 7,6 with Modal
Hello, today we are going to add a little feature to our CRUD app
laravel 8 CRUD, which is using bootstrap Modal to create, edit and view our projects, this can be extended to anything you want to do in Laravel 6/7/8 that requires displaying in a modal.
Modal helps us to work on another page without moving out of the current page, which helps not to lose sight of where we are. We are going to be using Bootstrap, Jquery, and Ajax to achieve this. Jquery and Ajax will send the URL with the id of the item we want to edit or view into the modal dynamically.
As it is always my culture, I will simplify this article to the level of any developer with screenshots and the code snippets, you can visit the GIthub repo if you just need the code or you made a mistake in following the article.

Click on my profile to follow me to get more updates.

Like I said earlier, anything that requires performing an action on another page and coming back can be achieved with a modal without navigating to different pages. All you need is to copy the code in the script tag and change the id of the button to be click and also the id of the modal to be display.
I will be displaying two sizes of the modal, one for small modal and the other for a medium modal, bootstrap has different sizes, small (modal-sm), medium (default), large (modal-lg).
small modal view
medium modal view
large modal view

Step 1: Setup the app
git clone https://github.com/Kingsconsult/laravel_8_crud.git
cd laravel_8_crud/
composer install
npm install
cp .env.example .env
php artisan key:generate
Add your database config in the .env file (you can check my articles on how to achieve that)
php artisan migrate
php artisan serve (if the server opens up, http://127.0.0.1:8000, then we are good to go)localhost
Navigate to http://127.0.0.1:8000/projectsproject index
Click on the Green button at the top-right to create some projectscreate projectcreate project
Step 2: Add the bootstrap, jquery, ajax script tag
In the head section of the app.blade.php in resources/views/layouts/ directory, add the following scripts below

<title>App Name - @yield('title')</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<!-- Script -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' type='text/javascript'></script>

<!-- Font Awesome JS -->
<script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"> </script>
<script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"> </script>

<link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' rel='stylesheet' type='text/css'>

<style>
    .footer {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        background-color: #9C27B0;
        color: white;
        text-align: center;
    }
    body {
        background-color: #EDF7EF
    }

</style>
Step 3: edit the index.blade.php
Go to the index.blade.php in resources/views/projects/ directory, copy, and paste the code below.

index.blade.php
@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Laravel 8 CRUD </h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                    data-attr="{{ route('projects.create') }}" title="Create a project"> <i class="fas fa-plus-circle"></i>
                </a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered table-responsive-lg table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Name</th>
                <th scope="col" width="30%">Introduction</th>
                <th scope="col">Location</th>
                <th scope="col">Cost</th>
                <th scope="col">Date Created</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td scope="row">{{ ++$i }}</td>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->introduction }}</td>
                    <td>{{ $project->location }}</td>
                    <td>{{ $project->cost }}</td>
                    <td>{{ date_format($project->created_at, 'jS M Y') }}</td>
                    <td>
                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST">

                            <a data-toggle="modal" id="smallButton" data-target="#smallModal"
                                data-attr="{{ route('projects.show', $project->id) }}" title="show">
                                <i class="fas fa-eye text-success  fa-lg"></i>
                            </a>

                            <a class="text-secondary" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                                data-attr="{{ route('projects.edit', $project->id) }}">
                                <i class="fas fa-edit text-gray-300"></i>
                            </a>
                            @csrf
                            @method('DELETE')

                            <button type="submit" title="delete" style="border: none; background-color:transparent;">
                                <i class="fas fa-trash fa-lg text-danger"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {!! $projects->links() !!}


    <!-- small modal -->
    <div class="modal fade" id="smallModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="smallBody">
                    <div>
                        <!-- the result to be displayed apply here -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- medium modal -->
    <div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mediumBody">
                    <div>
                        <!-- the result to be displayed apply here -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // display a modal (small modal)
        $(document).on('click', '#smallButton', function(event) {
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#smallModal').modal("show");
                    $('#smallBody').html(result).show();
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });

        // display a modal (medium modal)
        $(document).on('click', '#mediumButton', function(event) {
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#mediumModal').modal("show");
                    $('#mediumBody').html(result).show();
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });

    </script>
@endsection
From our code above, in the anchor tag to create a project, we remove the href=” ” and replace it with data-attr=” “, we also added data-toggle=” “, id=” “ and data-target=” “.
We created two div below our table with a class of “modal fade”, these div displays different sizes of modal with the heading specified and contents of the modal. Inside the modal div, there is a div with class modal-dialog, this is where we can change the size of the modal.
Lastly, we added a script tag that contains jquery, ajax logic in displaying the modal.
What we did in the script tag

On click of the button in the anchor tag, it should grab the URL found in the anchor tag
If the URL is valid and on success, it will grab the id of the target modal and pass the contents of the URL to the body of the modal
If there is an error with the URL, it will alert the user with the error message that the URL cannot be open
Step 4: edit the create, edit, show pages
Go to the create.blade.php, edit.blade.php and show.blade.php in resources/views/projects/ directory, and remove the extension of the base layouts,
@extends(‘layouts.app’)
@section(‘content’)
@endsection
That is all, we are good to go

clicking Create buttonCreate page
clicking the show buttonshow page
clicking the edit buttonCreate pageYou can follow me, leave a comment below, suggestion, reaction, or email, WhatsApp or call me, my contacts are on the screenshot. Visit my other posts
kingsconsult image 
Laravel 8 CRUD App, A simple guide
Kingsconsult ・ Sep 9 ・ 10 min read
#laravel8 #mvc #crud #laravel
kingsconsult image 
Laravel 8 Auth (Registration and Login)
Kingsconsult ・ Sep 17 ・ 4 min read
#beginners #webdev #php #laravel
Bootstrap Modal (2 Part Series)
1
How to create modal in Laravel 8 and Laravel 6/7 with AJax
2
How to implement Delete Confirmation in Laravel 8, 7,6 with Modal
Top comments (6)

Subscribe
pic
Add to the discussion
 
faeza97 profile image
Faeza Mohammed
•
Dec 8 '20

How to make the validation work inside your modal?


1
 like
Like
Reply
 
kingsconsult profile image
Kingsconsult 
•
Dec 8 '20

which kind of validation are you talking about?


2
 likes
Like
Reply
 
faeza97 profile image
Faeza Mohammed
•
Dec 8 '20 • Edited on Dec 8

Laravel validator is not working inside the modal.
how to make it work? while inputting blank values it will just refresh the page after submit button.
blade


@if ($errors->any())
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
controller
 $request->validate([
            'name' => 'required',
            'introduction' => 'required',
            'location' => 'required',
            'cost' => 'required'
        ]);
        $project->update($request->all());

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully');
    }

Like
Thread
faeza97 profile image
Faeza Mohammed
•
Dec 8 '20 • Edited on Dec 8

Look when I erase the input values and submit the form again it won't show the error messages for laravel validator

dev-to-uploads.s3.amazonaws.com/i/...


Like
Thread
christophebossens profile image
Christophe Bossens
•
Mar 22 '21

Probably a bit late, but if it doesn't help you it might still be useful for other people. I think you are mixing two approaches:

First, you can submit a form by sending a POST request to the server, and the server will then redirect you to a new page. In that case you can use the $errors directive to catch anything that went wrong during validation.

When using a modal, you submit your data using AJAX. This means you basically never leave the page you are on. The server will still return a response, but you have to process this in your JavaScript (specifically, in the AJAX error callback function).

You can find more info here: stackoverflow.com/questions/493337...


1
 like
Like
Reply
 
joseantoniobsi profile image
joseantoniobsi
•
Feb 3 '21

Very good! Thanks for the contribution!


1
 like
Like
Reply
Code of Conduct • Report abuse
🌚 Browsing with dark mode makes you a better developer.
It's a scientific fact.

Read next
laraveltuts profile image
Laravel 9 Vue JS CRUD App using Vite Example
Laravel Tuts - Jul 24

shanisingh03 profile image
How to login with username instead of email in Laravel ?
shani singh - Jul 23

dcblog profile image
Laravel organise migrations into folders
David Carr - Jul 12

celeron profile image
Spinning up MySQL Database with Docker
Khushal Bhardwaj - Jul 21


Kingsconsult
Follow
I am Kingsley Okpara, a Python and PHP Fullstack Web developer and tech writer, I also have extensive knowledge and experience with JavaScript while working on applications developed with VueJs.
LOCATION
Lagos, Nigeria
EDUCATION
Bsc.ed Mathematics, Enugu State University of Science and Technology, Enugu, Nigeria
WORK
Mid-level Web Developer at Plexada-Si
JOINED
Aug 5, 2019
More from Kingsconsult
Laravel Credit Card Validation
#php #laravel #webdev #security
Schedule a task to run at a specific time in laravel (CronJob)
#php #laravel #webdev #aws
Customize Laravel Jetstream (Registration and Login)
#jetstream #laravel #php #webdev
@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Laravel 8 CRUD </h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                    data-attr="{{ route('projects.create') }}" title="Create a project"> <i class="fas fa-plus-circle"></i>
                </a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered table-responsive-lg table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Name</th>
                <th scope="col" width="30%">Introduction</th>
                <th scope="col">Location</th>
                <th scope="col">Cost</th>
                <th scope="col">Date Created</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td scope="row">{{ ++$i }}</td>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->introduction }}</td>
                    <td>{{ $project->location }}</td>
                    <td>{{ $project->cost }}</td>
                    <td>{{ date_format($project->created_at, 'jS M Y') }}</td>
                    <td>
                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST">

                            <a data-toggle="modal" id="smallButton" data-target="#smallModal"
                                data-attr="{{ route('projects.show', $project->id) }}" title="show">
                                <i class="fas fa-eye text-success  fa-lg"></i>
                            </a>

                            <a class="text-secondary" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                                data-attr="{{ route('projects.edit', $project->id) }}">
                                <i class="fas fa-edit text-gray-300"></i>
                            </a>
                            @csrf
                            @method('DELETE')

                            <button type="submit" title="delete" style="border: none; background-color:transparent;">
                                <i class="fas fa-trash fa-lg text-danger"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {!! $projects->links() !!}


    <!-- small modal -->
    <div class="modal fade" id="smallModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="smallBody">
                    <div>
                        <!-- the result to be displayed apply here -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- medium modal -->
    <div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mediumBody">
                    <div>
                        <!-- the result to be displayed apply here -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // display a modal (small modal)
        $(document).on('click', '#smallButton', function(event) {
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#smallModal').modal("show");
                    $('#smallBody').html(result).show();
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });

        // display a modal (medium modal)
        $(document).on('click', '#mediumButton', function(event) {
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#mediumModal').modal("show");
                    $('#mediumBody').html(result).show();
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });

    </script>
@endsection


</div>
<!-- /PDF with image -->


<!-- Column selectors -->
<div class="card">
<div class="card-header">
    <h5 class="card-title">Column selectors</h5>
</div>

<div class="card-body">
    All of the data export buttons have a <code>exportOptions</code> option which can be used to specify information about what data should be exported and how. In this example the copy button will export column index 0 and all visible columns, the Excel button will export only the visible columns and the PDF button will export column indexes 0, 1, 2 and 5 only. Column visibility controls are also included so you can change the columns easily.
</div>

<table class="table datatable-button-html5-columns">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Job Title</th>
            <th>DOB</th>
            <th>Status</th>
            <th>Salary</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Marth</td>
            <td><a href="#">Enright</a></td>
            <td>Traffic Court Referee</td>
            <td>22 Jun 1972</td>
            <td><span class="badge badge-success">Active</span></td>
            <td>$85,600</td>
        </tr>
        <tr>
            <td>Jackelyn</td>
            <td>Weible</td>
            <td><a href="#">Airline Transport Pilot</a></td>
            <td>3 Oct 1981</td>
            <td><span class="badge badge-secondary">Inactive</span></td>
            <td>$106,450</td>
        </tr>
        <tr>
            <td>Aura</td>
            <td>Hard</td>
            <td>Business Services Sales Representative</td>
            <td>19 Apr 1969</td>
            <td><span class="badge badge-danger">Suspended</span></td>
            <td>$237,500</td>
        </tr>
        <tr>
            <td>Nathalie</td>
            <td><a href="#">Pretty</a></td>
            <td>Drywall Stripper</td>
            <td>13 Dec 1977</td>
            <td><span class="badge badge-info">Pending</span></td>
            <td>$198,500</td>
        </tr>
        <tr>
            <td>Sharan</td>
            <td>Leland</td>
            <td>Aviation Tactical Readiness Officer</td>
            <td>30 Dec 1991</td>
            <td><span class="badge badge-secondary">Inactive</span></td>
            <td>$470,600</td>
        </tr>
        <tr>
            <td>Maxine</td>
            <td><a href="#">Woldt</a></td>
            <td><a href="#">Business Services Sales Representative</a></td>
            <td>17 Oct 1987</td>
            <td><span class="badge badge-info">Pending</span></td>
            <td>$90,560</td>
        </tr>
        <tr>
            <td>Sylvia</td>
            <td><a href="#">Mcgaughy</a></td>
            <td>Hemodialysis Technician</td>
            <td>11 Nov 1983</td>
            <td><span class="badge badge-danger">Suspended</span></td>
            <td>$103,600</td>
        </tr>
        <tr>
            <td>Lizzee</td>
            <td><a href="#">Goodlow</a></td>
            <td>Technical Services Librarian</td>
            <td>1 Nov 1961</td>
            <td><span class="badge badge-danger">Suspended</span></td>
            <td>$205,500</td>
        </tr>
        <tr>
            <td>Kennedy</td>
            <td>Haley</td>
            <td>Senior Marketing Designer</td>
            <td>18 Dec 1960</td>
            <td><span class="badge badge-success">Active</span></td>
            <td>$137,500</td>
        </tr>
        <tr>
            <td>Chantal</td>
            <td><a href="#">Nailor</a></td>
            <td>Technical Services Librarian</td>
            <td>10 Jan 1980</td>
            <td><span class="badge badge-secondary">Inactive</span></td>
            <td>$372,000</td>
        </tr>
        <tr>
            <td>Delma</td>
            <td>Bonds</td>
            <td>Lead Brand Manager</td>
            <td>21 Dec 1968</td>
            <td><span class="badge badge-info">Pending</span></td>
            <td>$162,700</td>
        </tr>
        <tr>
            <td>Roland</td>
            <td>Salmos</td>
            <td><a href="#">Senior Program Developer</a></td>
            <td>5 Jun 1986</td>
            <td><span class="badge badge-secondary">Inactive</span></td>
            <td>$433,060</td>
        </tr>
        <tr>
            <td>Coy</td>
            <td>Wollard</td>
            <td>Customer Service Operator</td>
            <td>12 Oct 1982</td>
            <td><span class="badge badge-success">Active</span></td>
            <td>$86,000</td>
        </tr>
        <tr>
            <td>Maxwell</td>
            <td>Maben</td>
            <td>Regional Representative</td>
            <td>25 Feb 1988</td>
            <td><span class="badge badge-danger">Suspended</span></td>
            <td>$130,500</td>
        </tr>
        <tr>
            <td>Cicely</td>
            <td>Sigler</td>
            <td><a href="#">Senior Research Officer</a></td>
            <td>15 Mar 1960</td>
            <td><span class="badge badge-info">Pending</span></td>
            <td>$159,000</td>
        </tr>
    </tbody>
</table>
</div>
<!-- /column selectors -->


<!-- Tab separated values -->
<div class="card">
<div class="card-header">
    <h5 class="card-title">Tab separated values</h5>
</div>

<div class="card-body">
    The <code>copyHtml5</code> and <code>csvHtml5</code> buttons have the option to specify the character that separates the values between fields. By default this is a <strong>tab</strong> character for the <strong>copy</strong> button, but the <strong>Comma</strong> Separated Values button uses a comma. In this example the exported file is a tab separated values file. The file extension has also been set to reflect this, although that is optional as most spreadsheet applications will read TSV files without issue.
</div>

<table class="table datatable-button-html5-tab">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Job Title</th>
            <th>DOB</th>
            <th>Status</th>
            <th>Salary</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Marth</td>
            <td><a href="#">Enright</a></td>
            <td>Traffic Court Referee</td>
            <td>22 Jun 1972</td>
            <td><span class="badge badge-success">Active</span></td>
            <td>$85,600</td>
        </tr>
        <tr>
            <td>Jackelyn</td>
            <td>Weible</td>
            <td><a href="#">Airline Transport Pilot</a></td>
            <td>3 Oct 1981</td>
            <td><span class="badge badge-secondary">Inactive</span></td>
            <td>$106,450</td>
        </tr>
        <tr>
            <td>Aura</td>
            <td>Hard</td>
            <td>Business Services Sales Representative</td>
            <td>19 Apr 1969</td>
            <td><span class="badge badge-danger">Suspended</span></td>
            <td>$237,500</td>
        </tr>
        <tr>
            <td>Nathalie</td>
            <td><a href="#">Pretty</a></td>
            <td>Drywall Stripper</td>
            <td>13 Dec 1977</td>
            <td><span class="badge badge-info">Pending</span></td>
            <td>$198,500</td>
        </tr>
        <tr>
            <td>Sharan</td>
            <td>Leland</td>
            <td>Aviation Tactical Readiness Officer</td>
            <td>30 Dec 1991</td>
            <td><span class="badge badge-secondary">Inactive</span></td>
            <td>$470,600</td>
        </tr>
        <tr>
            <td>Maxine</td>
            <td><a href="#">Woldt</a></td>
            <td><a href="#">Business Services Sales Representative</a></td>
            <td>17 Oct 1987</td>
            <td><span class="badge badge-info">Pending</span></td>
            <td>$90,560</td>
        </tr>
        <tr>
            <td>Sylvia</td>
            <td><a href="#">Mcgaughy</a></td>
            <td>Hemodialysis Technician</td>
            <td>11 Nov 1983</td>
            <td><span class="badge badge-danger">Suspended</span></td>
            <td>$103,600</td>
        </tr>
        <tr>
            <td>Lizzee</td>
            <td><a href="#">Goodlow</a></td>
            <td>Technical Services Librarian</td>
            <td>1 Nov 1961</td>
            <td><span class="badge badge-danger">Suspended</span></td>
            <td>$205,500</td>
        </tr>
        <tr>
            <td>Kennedy</td>
            <td>Haley</td>
            <td>Senior Marketing Designer</td>
            <td>18 Dec 1960</td>
            <td><span class="badge badge-success">Active</span></td>
            <td>$137,500</td>
        </tr>
        <tr>
            <td>Chantal</td>
            <td><a href="#">Nailor</a></td>
            <td>Technical Services Librarian</td>
            <td>10 Jan 1980</td>
            <td><span class="badge badge-secondary">Inactive</span></td>
            <td>$372,000</td>
        </tr>
        <tr>
            <td>Delma</td>
            <td>Bonds</td>
            <td>Lead Brand Manager</td>
            <td>21 Dec 1968</td>
            <td><span class="badge badge-info">Pending</span></td>
            <td>$162,700</td>
        </tr>
        <tr>
            <td>Roland</td>
            <td>Salmos</td>
            <td><a href="#">Senior Program Developer</a></td>
            <td>5 Jun 1986</td>
            <td><span class="badge badge-secondary">Inactive</span></td>
            <td>$433,060</td>
        </tr>
        <tr>
            <td>Coy</td>
            <td>Wollard</td>
            <td>Customer Service Operator</td>
            <td>12 Oct 1982</td>
            <td><span class="badge badge-success">Active</span></td>
            <td>$86,000</td>
        </tr>
        <tr>
            <td>Maxwell</td>
            <td>Maben</td>
            <td>Regional Representative</td>
            <td>25 Feb 1988</td>
            <td><span class="badge badge-danger">Suspended</span></td>
            <td>$130,500</td>
        </tr>
        <tr>
            <td>Cicely</td>
            <td>Sigler</td>
            <td><a href="#">Senior Research Officer</a></td>
            <td>15 Mar 1960</td>
            <td><span class="badge badge-info">Pending</span></td>
            <td>$159,000</td>
        </tr>
    </tbody>
</table>
</div>
<!-- /tab separated values -->






        </div>

    </div>

</section>



@endsection

@push('plugin-scripts')
{!! Html::script('assets/js/loader.js') !!}
{!! Html::script('plugins/apex/apexcharts.min.js') !!}
{!! Html::script('plugins/flatpickr/flatpickr.js') !!}
{!! Html::script('assets/js/dashboard/dashboard_1.js') !!}


@endpush


<script>
     $.fn.dataTable.ext.errMode = 'none';

$('#table').on( 'error.dt', function ( e, settings, techNote, message ) {
console.log( 'An error has been reported by DataTables: ', message );
} ) ;


</script>
               