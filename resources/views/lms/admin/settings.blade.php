@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            General settings
        </h1>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <!-- Auth Infusionsoft user -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Authenticate Infusionsoft application</div>
                </div>

                <form method="post">

                    <div class="box-footer">
                        <a href="{{ InfusionsoftFlow::requestUrl() }}" class="btn btn-primary">Auth</a>
                    </div>

                    {{ csrf_field() }}
                </form>
            </div> <!-- END Auth Infusionsoft user -->

            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Sync Infusionsoft tags</div>
                </div>

                <form method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Select Infusionsoft tag categories to sync:</label>
                            <select multiple="" name="is_tag_categories[]" class="form-control" style="min-height: 350px;">
                                @foreach($is_tag_categories as $tag_cat)
                                    <option value="{{ $tag_cat['Id'] }}">{{ $tag_cat['CategoryName'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                    {{ csrf_field() }}
                </form>
            </div>

            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Auto login</div>
                </div>

                <form method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Auto login auth-key:</label>
                            <input type="text" class="form-control" name="auto_login_key" value="{{ @$auto_login_key }}" />
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                    {{ csrf_field() }}
                </form>
            </div>

            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Max IP logins per user</div>
                </div>

                <form method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Number: (by default: 10)</label>
                            <input type="text" class="form-control" name="max_ip_logins" value="{{ @$max_ip_logins }}" />
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
@endsection