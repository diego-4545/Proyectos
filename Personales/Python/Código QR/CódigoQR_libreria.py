import qrcode

data = "https://www.uanl.mx/"
qr = qrcode.QRCode(
    version=None,  
    box_size=15,  
    border=3,
)
qr.add_data(data)
qr.make(fit=True)
img = qr.make_image(fill="black", back_color="white")
img.save("C:\Proyectos\Proyectos\Personales\Python\Código QR\qr.png")
