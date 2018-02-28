#!/usr/bin/env python3

from escpos import *
import requests, sys

# obtengo los pedidos pendienes de impresion 
payload = { 'key': 'd6e4ad5462e40465d915bfc2afb3504a' }
r = requests.get('http://vllanten.no-ip.org/impresion/index', params=payload)
pedidos = r.json()

if(pedidos["success"] is True):
    # Hay ordenes por imprimir

    # Identifico la impresora
    Epson = printer.Usb(0x04b8,0x0e15)

    # recorro los pedidos que traje
    for p in pedidos["data"]:

        Epson.text("\n"+pedidos["cabecera"]+"\n")
        Epson.text("ID: "+str(p['id'])+"\n")
        Epson.text("Fecha del pedido: "+p["fecha_creacion"]+"\n")
        
        Epson.text("##Cliente:\n")
        Epson.text(p["cliente"]["persona"]["nombres"]+" ")
        Epson.text(p["cliente"]["persona"]["ap_pat"]+" ")
        Epson.text(p["cliente"]["persona"]["ap_mat"]+"\n")
        Epson.text("Celular: "+p["cliente"]["persona"]["celular"]+"\n")
        
        Epson.text("##Direccion:\n")
        Epson.text(p["direccion"]["descripcion"]+"\n")
        Epson.text("Sector: "+ p["direccion"]["sector"]["descripcion"]+"\n" )
        
        Epson.text("##Observacion:\n")
        Epson.text(p["observacion"]+"\n")
        
        Epson.text("##Detalle\n")
        for pr in p["productos"]:
            Epson.text(str(pr["cantidad"])+" x ")
            Epson.text(pr["producto"]["nombre"])
            if(pr["especificacion"]):
                Epson.text(' - ')
                Epson.text(pr["especificacion"]["descripcion"]+"\n")
            else:
                Epson.text("\n")

        Epson.text("\nTotal: $"+str(p["total"])+"\n")

        # Corto la hoja
        Epson.cut()