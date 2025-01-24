import xml.etree.ElementTree as ET

class Svg(object):

    def __init__(self):
        self.root = ET.Element('svg',xmlns="http://www.w3.org/2000/svg")

    def setSize(self,height, width):
        self.root.set("height",height)
        self.root.set("width",width)

    def addPolyline(self,points,color,width):
        polyline = ET.SubElement(self.root,"polyline")
        polyline.set("points",points)
        style = "fill:none;stroke:"+color+";stroke-width:"+width
        polyline.set("style",style)

    def writeSvg(self,fileName):
        tree = ET.ElementTree(self.root)
        tree.write(fileName, encoding='utf-8', xml_declaration=True)

def main():
    height = 800
    multiplier=100
    fileName = input("Introduzca el nombre del archivo .xml    = ")

    newSvg = Svg()

    try:
        root = ET.parse(fileName).getroot()
    except IOError:
        print ('No se encuentra el archivo ', fileName)
        exit()
    except ET.ParseError:
        print("Error procesando en el archivo XML = ", fileName)
        exit()

    namespace={'xs':'http://www.uniovi.es'}
    circuitAltitudes = "10,"+str(height*0.75)+" 10,"+str(height-float(root.find('.//xs:tramos[last()]//xs:altitud',namespace).text)*multiplier)
    x = 10.0
    for coord in root.findall('.//xs:tramo',namespace):
        nodeDistance=coord.find('xs:distancia',namespace)
        if(nodeDistance.get("unidades") != "m"):
            print("Error: las distancias de tramos deben estar en metros")
            exit()
        circuitAltitudes+=" "
        x+=float(nodeDistance.text)*0.1
        circuitAltitudes+=str(x)
        altitud = height - float(coord.find('./xs:coordenadas/xs:altitud',namespace).text)*multiplier
        circuitAltitudes+=","+str(altitud)
    circuitAltitudes+=" "+str(x)+","+str(height*0.75)+" 10,"+str(height*0.75)

    width = x +10
    
    newSvg.addPolyline(circuitAltitudes,"green","5")


    outputName  = input("Introduzca el nombre del archivo generado (*.svg) = ")+".svg"
    newSvg.setSize(str(height),str(width))
    newSvg.writeSvg(outputName)
    print("Creado el archivo: ", outputName)
   

if __name__ == "__main__":
    main()    