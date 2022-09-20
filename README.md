# Spanish Municipalities Home Rent Table
🗺️ Web Visualization for the public data of the Spanish Home Rent Index

![image](https://user-images.githubusercontent.com/11091182/191220096-776a2956-3609-43aa-843e-805f88dc0c52.png)

## Description:
This repository uses [datatables.net](https://www.datatables.net/) JavaScript library to make a table visualization of the Spanish Rent Index by municipalities.

## Installation:
Just download the repository and open the `index.html` file in your browser

## Customization:
Data was created using the [spanish_rent_price_index](https://github.com/jibion/spanish_rent_price_index) repository, but you can customize it with your own data changing the following lines accordingly to point to your data source:

```
$(document).ready(function () {
            $.ajax({
                url: "./data/data_vc.json",
```

## Live Demo:
[Stage version](https://hackthedad.com/provincias_mapa/) > Select "Tabla de Municipios" Tab