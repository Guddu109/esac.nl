<div class="card mt-4">
    <div class="card-header">
        <h3>{{trans('user.introPackageOptions')}}</h3>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-12">
                {!! Form::label('package_type', trans('user.introPackage')) !!}
                {!! Form::select('package_type',trans('user.packageTypes'), null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                {!! Form::label('shirt_size', trans('user.tshirt')) !!}
                {!! Form::select('shirt_size',trans('user.shirtSizes'), null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('intro_weekend_date', trans('user.introWeekend')) !!}
                {!! Form::select('intro_weekend_date',trans('user.weekendDates'), null, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
</div>