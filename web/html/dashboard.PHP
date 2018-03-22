<!doctype html>
<html lang="de">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="Tim Schneider"

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" href="../css/main.css">
    <title>Dashboard</title>
    
    <style>
    	
    	
    	.container-fluid{
    		text-align: center;
    	}
    	
    	.card-title{
    		font-size: 2rem;
    	}
    	
    	.dropdown-item {
    		color:white;
    	}
    	
    	
    </style>
    
  
  </head>
  <body>
  	<div class="container-fluid">
  		
  		<div class="container">
  			<div class="card-deck">
  				<div class="card text-white bg-dark mb-3" >
  					<div class="card-body">
    					<h5 class="card-title">Dashboard Projektwahl</h5>
    					<p class="card-text">Übersicht über die Projektwahl-Datenbank</p>
  					</div>
					</div>
				</div>
			</div>
  		
	  	
  		
  		<div class="container">
  			<div class="card-deck">
  				
				  <div class="card text-white bg-dark mb-3" >
  						<div class="card-body">
    						<h5 class="card-title" id="eingereichteProjekte"><?php echo count(file('../csv/projekte.csv'));?></h5>
    						<p class="card-text">Projekte wurden eingereicht</p></p>
  						</div>
					</div>
				  
				  <div class="card text-white bg-dark mb-3" >
  						<div class="card-body">
    						<h5 class="card-title" id="anzahlPlaetze">###</h5>
    						<p class="card-text">Plätze sind verfügbar</p></p>
  						</div>
					</div>
					
					<div class="card text-white bg-dark mb-3" >
  						<div class="card-body">
    						<h5 class="card-title" id="schuelergewaehlt">###</h5>
    						<p class="card-text">Schüler haben schon gewählt</p>
  						</div>
					</div>
					  
			</div>
  		</div>
  		
  		
  		<div class="container">
  			<div class="progress" style= "height: 4em;" >
  				<div class="progress-bar bg-dark" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
				</div>
				<p> ### von ### Schülern haben bereits gewählt</p>
  		</div>
  		
  		<div class="container">
  			<div class="dropdown" >
  					<button class="btn btn-secondary dropdown-toggle btn-lg bg-dark" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  						
    				Datenbankverwaltung
    				
  					</button>
  				<div class="dropdown-menu bg-dark" aria-labelledby="dropdownMenuButton">
    				<a class="dropdown-item" href="#">Projekte exportieren</a>
    				<a class="dropdown-item" href="#">Another Action</a>
    				<div class="dropdown-divider"></div>
    				<a class="dropdown-item" href="#">Daten aus Datenbank löschen</a>
  				</div>
				</div>
  		</div>
  		

  	</div>
 
  	
  		
    
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>