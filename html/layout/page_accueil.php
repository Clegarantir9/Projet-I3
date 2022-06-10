<div id="pageAccueil" >
	<div class="back capteurtxt" id="local">
			
                <?php
				// Database settings
				    $hName='10.199.132.11'; // host name
   					$uName='grp5pab';   // database user name
   					$password='wKtQ898v';   // database password
   					$dbName = "grp5pabdb"; // database name
   					$dbCon = mysqli_connect($hName,$uName,$password,"$dbName");
   					  if(!$dbCon){
   					      die('Could not Connect MySql Server:' .mysql_error());
   					  }
				?>
		<div class="spacertxt"></div>
		<div class="spacertxt"></div>
		<h1>Nos Capteurs</h1>
		<?php
				
			$sql = 'SELECT date FROM data_arduino order by date desc LIMIT 1;';
			$result=mysqli_query($dbCon,$sql);
			$singleRow = mysqli_fetch_assoc($result);				
												
				?>
		<h3>Dernière mise a jour des capteurs : <?php echo $singleRow['date'];?> </h3>

		<table class="hrstyle">

     <th colspan="2">
 
          <h1> Nombre de personnes dans le bâtiment </h1>
              
     </th>
               <?php
				
					$sql = 'SELECT SUM(io) FROM data_esp WHERE date >= cast((now()) as date) and date < cast((now() + interval 1 day) as date);';
					$result=mysqli_query($dbCon,$sql);
					$singleRow = mysqli_fetch_assoc($result);				
												
				?>
         <th colspan="2" class="tohide">
	   
             <h1> Qualité de l'air </h1>
              
         </th>

            <tr>
              		<td class="col">
						<div class="gauge-wrap gwsolo" data-value="<?php echo $singleRow['SUM(io)']/4+1;?>"></div>
					</td>
					<td class="col">
              			<h1 id="personne"><?php echo $singleRow['SUM(io)'];?> <a>   / 400 Personnes</a></h1>							
					</td>                

                    <th colspan="2" class="toshow">
	   
                	<h1> Qualité de l'air </h1>
              
            		</th>

					
					<td class="col">
              			<h1 id="quality"><?php 

							$sql = 'SELECT airquality FROM data_arduino order by date desc LIMIT 1;';
							$result=mysqli_query($dbCon,$sql);
							$singleRow = mysqli_fetch_assoc($result);


							if ($singleRow['airquality'] == 3){echo "Air frais";}
							if ($singleRow['airquality'] == 2){echo "Air peu pollué";}
							if ($singleRow['airquality'] == 1){echo "Air pollué";}
							if ($singleRow['airquality'] == 0){echo "Air pollué ";}

						?></h1>							
					</td>  


	</tr>
		
</table>

		<table class="hrstyle">

			<th >
 
                <h1> Nombre d'entrées </h1>
              
            </th>
            <th  class="tohide">
	   
                <h1> Nombre de sorties </h1>
              
            </th>
				<?php //entree
					$sql = 'SELECT SUM(io) as entrée FROM data_esp WHERE io > 0 and date >= cast((now()) as date) and date < cast((now() + interval 1 day) as date);';
					$result=mysqli_query($dbCon,$sql);
					$singleRow = mysqli_fetch_assoc($result);
				?>
            <tr>

              	<td class="col">
              		<h1 id="entree"><?php echo $singleRow['entrée']*1;?>  Personnes</h1>							
				</td>

				<?php //entree
					$sql = 'SELECT SUM(io) as sortie FROM data_esp WHERE io < 0 and date >= cast((now()) as date) and date < cast((now() + interval 1 day) as date);';
					$result=mysqli_query($dbCon,$sql);
					$singleRow = mysqli_fetch_assoc($result);
				?>

				<th  class="toshow">
	   
                	<h1> Nombre de sorties </h1>
              
            	</th>

				<td class="col">
              		<h1 id="sortie"><?php echo $singleRow['sortie']*-1;?>  Personnes </h1>							
				</td>

			</tr>

        	</table >

			<?php
					$sql = "SELECT * FROM data_arduino order by date desc LIMIT 1";
					$result=mysqli_query($dbCon,$sql);
					$singleRow = mysqli_fetch_assoc($result);
					
				?>
				
			<table  class="hrstyle">

			<th colspan="2">
                		   
            	<h1>Qualité sonore </h1>
              
            </th>
            <tr>
				<td class="col">
        			<div class="gauge-wrap gwsolo" data-value="<?php echo $singleRow['son']-15;?>"></div>
 				</td>
				<td class="col">
						<h1 ><?php echo $singleRow['son'];?><a> dB</a></h1>
			</td>	
				
			</tr>
		
			</table>

			<table class="hrstyle">
				<tr>
					<th colspan="2"  >
 
                		<h1> Température </h1>
              
            		</th>
            		<th colspan="2" class="tohided" >
	   
                		<h1> Taux d'humidité </h1>
              
            		</th>
                 </tr>
            	<tr>
            	    <td class="col duo">
						<div class="gauge-wrapt" data-value="<?php echo ($singleRow['temp']+15)*1.53;?>"></div><!--good de 20 a 30 -->
					</td>
					<td class="col duo">
              			<h1 id="temperature"><?php echo $singleRow['temp'];?>°C</h1>							
					</td>    

					<th class="toshowd">
 
                		<h1> Taux d'humidité </h1>
              
            		</th>
            	  
					<td class="col duo">
						<div class="gauge-wrapt" data-value="<?php echo $singleRow['humidite'];?>"></div><!-- good de 40 a 60%-->
					</td>
					<td class="col duo">
              			<h1 id="humidite"><?php echo $singleRow['humidite'];?>%  d'humidité</h1>							
					</td>	

				</tr>
		
			</table>

			<table class="hrstyle">
         
				<tr>
            		<th colspan="2">
 
                		<h1> Monoxyde de carbone (CO) </h1>
              
            		</th>
            		<th colspan="2" class="tohide">
	   
                		<h1> Dioxyde d'azote (NO2) </h1>
              
            		</th>
				</tr>
            	<tr>
              		<td class="col">
						<div class="gauge-wrap" data-value="<?php echo $singleRow['gaz1']/10;?>"></div>
					</td>
					<td class="col">
              			<h1 id="gaz1"><?php echo $singleRow['gaz1'];?> ppm</h1>							
					</td>               

              
            		<th colspan="2" class="toshow">
	   
                		<h1> Dioxyde d'azote (NO2) </h1>
              
            		</th>

					<td class="col">
						<div class="gauge-wrap" data-value="<?php echo $singleRow['gaz2']/10;?>"></div>
					</td>
					<td class="col">
              			<h1 id="gaz2"><?php echo $singleRow['gaz2'];?> ppm</h1>							
					</td> 					


              	</tr>

              	<tr>
              		<th colspan="2">
 
                		<h1> Ethanol (C2H5OH) </h1>
              
            		</th>
          			<th colspan="2" class="tohide">
	   
                		<h1> Composés organiques volatils (VOC) </h1>
              
            		</th>
				</tr>

            	<tr>
              		<td class="col">
						<div class="gauge-wrap" data-value="<?php echo $singleRow['gaz3']/10;?>"></div>
					</td>
					<td class="col">
              			<h1 id="gaz3"><?php echo $singleRow['gaz3'];?> ppm</h1>							
					</td>               
 
              
          			<th colspan="2" class="toshow">
	   
                		<h1> Composés organiques volatils (VOC) </h1>
              
            		</th>

					<td class="col">
						<div class="gauge-wrap" data-value="<?php echo $singleRow['gaz4']/10;?>"></div>
					</td>
					<td class="col">
              			<h1 id="gaz4"><?php echo $singleRow['gaz4'];?> ppm</h1>							
					</td> 
 
              </tr>

			</table>



			<table class="hrstyle">

          		<th colspan="2">
 
                	<h1> Luminosité </h1>
              
            	</th>
          		<th colspan="2" class="tohide">
	   
                	<h1> Ensoleillement </h1>
              
            	</th>

            	<tr>
              		<td class="col">
						<div class="gauge-wrapt" data-value="<?php echo $singleRow['lumi']/5;?>"></div>
					</td>
					<td class="col">
              			<h1 id="lumi"><?php echo $singleRow['lumi'];?> Lux</h1>							
					</td>                

                    <th colspan="2" class="toshow">
	   
                	<h1> Ensoleillement </h1>
              
            		</th>

					<td class="col">
						<div class="gauge-wrap" data-value="<?php echo $singleRow['uv']*12.8+1;?>"></div>
					</td>
					<td class="col">
              			<h1 id="UV"><?php echo $singleRow['uv'];?> UV</h1>							
					</td>  


				</tr>
		
			</table>
			<div class="spacertxt"></div>
			

		</div>
 

</div>

<script src="js/gauges.js"></script>