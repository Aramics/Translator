
<h1>{{ $title }}  </h1>

<h3>{{session('flash')}} </h3>

			{!! Form::open(array('url' => 'import', 'method' => 'POST', 'novalidate'=>'','name'=>'formUsers ', 'files'=>'true')) !!}
				<div class="row">
					
	 				<div class="form-group col-md-6" >
	 					<label>Download Sample</label>
		 				<fieldset>
		 					
							<a href="{{ url('import/download') }}" class="btn btn-info lms-content">Export
							</a>

		 				</fieldset>
					 </div>

				

					<div class="form-group col-md-6">

						<fieldset >
							Import
						<label class="margintop30">Upload Excel</label>
							{{-- {{ Form::label('excel', __('upload_excel')) }} --}}
							 
							
						{!! Form::file('excel', array('class'=>'form-control','id'=>'excel_input', 'accept'=>'.xls,.xlsx', 'required'=>'true')) !!}
								 
								 
						 
						</fieldset>
	 				</div>
					
					<div class="buttons text-center">
						<button class="btn btn-lg btn-primary button">{{ __("import") }}</button>
					</div>

				</div> 
			{!! Form::close() !!}