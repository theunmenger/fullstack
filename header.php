<?php
    echo("
        <div id='header'>
            <a href='dashboard.php'><img id='logo' src='img/logo.png' alt='logo'></a>
            <div class='dropdown'>
                <button class='dropdownbtn'><h3>Toevoegen</h3></button>
                <div class='dropdown_content'>
                    <a href='add_product.php'><h3>Product toevoegen</h3></a>
                    <a href='add_fabriek.php'><h3>Fabriek toevoegen</h3></a>
                    <a href='add_locatie.php'><h3>Locatie toevoegen</h3></a>
                </div>
            </div>
            <a href='bestellingen.php'><h3>Bestellingen</h3></a>
            <a href='settings.php'><h3>Settings</h3></a>
            <a href='admin.php'><h3>Admin</h3></a>
            <a href='index.php'><h3>Logout</h3></a>
        </div>    
    ");
?>


