<?php include('header.php');

?><br>

<div class="w3-container w3-row w3-center w3-round-large" style="margin:0 auto;width:60%;"><br>
    <h1>Find your favorites car here! Total Finding</h1>
    <div class="w3-col w3-padding w3-container w3-card w3-round-large" style=" margin-bottom:10px">

        <form class="" method="GET" action='purchase_listing.php'>

            <input class="w3-col w3-input w3-round" style="width:90%" type="text" name="search"
                placeholder="What car are you looking for?">

            <div class="w3-col" style="width:10%">
                <button class="w3-button w3-round"><i class="material-icons" name='submit'>search</i></a>
            </div>


        </form>

    </div>
    <br><br>

    <?php

    if (!empty($_GET['search'])) {

        # Memanggil fail connection
        include('connection.php');

        # Mengambil data GET
        /* $model = $_GET['search']; */
        $model = "%". strtoupper($_GET['search']) . "%";
        
        ########################################################################################################################################

        # Arahan mencari data dari jadual car yang tidak wujud di jadual purchase
        /* $arahan_sql_cari = "
                SELECT car.*, model.modelName, images.image 
                FROM car, model, images 
                WHERE car.numPlate NOT IN (SELECT numPlate FROM purchase)
                AND car.model_ID = model.model_ID 
                AND car.idimg = images.idimg
                AND (car.carName LIKE '%' || :SEARCH || '%' 
                    OR model.modelName LIKE '%' || :SEARCH || '%' 
                    OR car.yearManufac LIKE '%' || :SEARCH || '%')"; */

        $arahan_sql_cari =  "
                SELECT 
                    CAR.*, 
                    MODEL.MODELNAME,
                    IMAGES.IDIMG
                FROM CAR
                INNER JOIN MODEL ON CAR.MODEL_ID = MODEL.MODEL_ID
                LEFT OUTER JOIN IMAGES ON CAR.IDIMG = IMAGES.IDIMG
                WHERE CAR.NUMPLATE NOT IN (SELECT NUMPLATE FROM PURCHASE)
                AND (
                    UPPER(CAR.CARNAME) LIKE UPPER('NISSAN')
                    OR UPPER(MODEL.MODELNAME) LIKE UPPER('NISSAN')
                )
            
        ";
        echo "$arahan_sql_cari  ". "$model";
        # Melaksanakan proses carian
        $laksana_arahan = oci_parse($condb, $arahan_sql_cari);

        # Bind parameter untuk carian
//oci_bind_by_name($laksana_arahan, ':search', $model);

        # Laksanakan arahan SQL
        oci_execute($laksana_arahan);
            echo oci_num_rows($laksana_arahan);
        # Jika terdapat data yang ditemui
        if (oci_fetch_all($laksana_arahan, $rekod, null, null, OCI_FETCHSTATEMENT_BY_ROW) > 0) {
            # Papar header kepada jadual
            echo "<i style='font-size: small;'> Showing " . count($rekod) . " result(s) of \" " . htmlspecialchars($model) . " \".</i><br><br>";

            # Memaparkan data yang ditemui semasa proses carian baris demi baris
            foreach ($rekod as $row) {
                # Mengumpulkan nilai tatasusunan untuk data GET
                $data_get = array(
                    'numPlate' => $row['NUMPLATE'],
                    'carName' => $row['CARNAME'],
                    'carType' => $row['CARTYPE'],
                    'color' => $row['COLOR'],
                    'yearManufac' => $row['YEARMANUFAC'],
                    'desccar' => $row['DESCCAR'],
                    'initialPrice' => $row['INITIALPRICE'],
                    'modelName' => $row['MODELNAME'],
                );

                echo $row['NUMPLATE']; 
                echo $row['CARNAME']; 
                echo $row['CARTYPE']; 
                echo $row['COLOR']; 
                echo $row['YEARMANUFAC']; 
                echo $row['DESCCAR']; 
                echo $row['INITIALPRICE']; 
                echo $row['MODELNAME']; 

                # Memaparkan maklumat kenderaan
                echo "
                    <a href=purchase_view.php?" . http_build_query($data_get) . ">
                    <div class='w3-col w3-container w3-card w3-round-large' style='margin-bottom:15px;text-align:left; width: 100%; height: 200px;'>
                        <div class='w3-quarter w3-border w3-center' style='height: 100%;'>";
    ?>



    <?php
                echo "
                        </div>
                        <div class='w3-threequarter' style='height:60px;padding-left:10px; padding-bottom:10px'>
                            <h3><b>" . htmlspecialchars($row['CARNAME']) . "</b></h3>
                        </div>
                        <div class='w3-threequarter' style='padding-top:5px'>
                            " . htmlspecialchars($row['CARTYPE']) . "
                            <div class='w3-half' style='height: 40px;padding-left:10px'>
                                " . htmlspecialchars($row['MODELNAME']) . "
                            </div>
                        </div>
                        <div class='w3-threequarter'>
                            " . htmlspecialchars($row['YEARMANUFAC']) . "
                            <div class='w3-half' style='height: 40px;padding-left:10px'>
                                " . htmlspecialchars($row['COLOR']) . "
                            </div>
                        </div>
                        <div class='w3-threequarter'>
                            <div class='w3-half' style='color:blue;text-align:right;height: 40px;padding-left:10px'>
                                <h3><b>RM " . htmlspecialchars($row['INITIALPRICE']) . "</b></h3>
                            </div>
                            <div class='w3-half' style='height: 40px;padding-top:5px'>
                                <a class='w3-button w3-round w3-right w3-round-xlarge' style='margin-left:5px; background: #FFBF00' href=purchase_view.php?" . http_build_query($data_get) . ">View More<br></a>
                                <a class='w3-button w3-round w3-right w3-round-xlarge' style='background: #FFBF00' href=purchase_payment.php?" . http_build_query($data_get) . ">Buy Now<br></a>
                            </div>
                        </div>
                    </div></a>";
            }
        } else {
            # Jika tiada data ditemui
            echo "<i><br><br>The car you are looking for may not be in stock or has been sold out.<br>You can continue searching next :)<br><br></i>";
        }
    }

    ?>

</div>
<br>

<?PHP include('footer.php'); ?>