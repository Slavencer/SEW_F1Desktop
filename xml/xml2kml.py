import xml.etree.ElementTree as ET
class Kml(object):

    def __init__(self):
        self.raiz = ET.Element('kml',xmlns="http://www.opengis.net/kml/2.2")
        self.doc = ET.SubElement(self.raiz,'Document')

    def addPlacemark(self,nombre,descripcion,long,lat,alt, modoAltitud):
        """
        Añade un elemento <Placemark> con puntos <Point>
        """
        pm = ET.SubElement(self.doc,'Placemark')
        ET.SubElement(pm,'name').text = '\n' + nombre + '\n'
        ET.SubElement(pm,'description').text = '\n' + descripcion + '\n'
        punto = ET.SubElement(pm,'Point')
        ET.SubElement(punto,'coordinates').text = '\n{},{},{}\n'.format(long,lat,alt)
        ET.SubElement(punto,'altitudeMode').text = '\n' + modoAltitud + '\n'

    def addLineString(self,nombre,extrude,tesela, listaCoordenadas, modoAltitud, color, ancho):
        """
        Añade un elemento <Placemark> con líneas <LineString>
        """

        estilo = ET.SubElement(self.doc, 'Style')
        estilo.set("id","style")
        linea = ET.SubElement(estilo, 'LineStyle')
        ET.SubElement (linea, 'color').text = '\n' + color + '\n'
        ET.SubElement (linea, 'width').text = '\n' + ancho + '\n'

        pm = ET.SubElement(self.doc,'Placemark')
        ET.SubElement(pm,'name').text = '\n' + nombre + '\n'
        ET.SubElement(pm,"styleUrl").text="#style"
        ls = ET.SubElement(pm, 'LineString')
        ET.SubElement(ls,'extrude').text = '\n' + extrude + '\n'
        ET.SubElement(ls,'tessellation').text = '\n' + tesela + '\n'
        ET.SubElement(ls,'coordinates').text = '\n' + listaCoordenadas + '\n'
        ET.SubElement(ls,'altitudeMode').text = '\n' + modoAltitud + '\n' 

    def writeKml(self,fileName):
        tree = ET.ElementTree(self.raiz)
        tree.write(fileName, encoding='utf-8', xml_declaration=True)

def main():

    fileName = input("Introduzca el nombre del archivo .xml    = ")

    newKml = Kml()

    try:
        root = ET.parse(fileName).getroot()
    except IOError:
        print ('No se encuentra el archivo ', fileName)
        exit()
    except ET.ParseError:
        print("Error procesando en el archivo XML = ", fileName)
        exit()

    circuitCoords = ""
    namespace={'xs':'http://www.uniovi.es'}
    coords =root.find('.//xs:coordenadas',namespace)
    long = coords.find('xs:longitud',namespace).text
    lat = coords.find('xs:latitud',namespace).text
    alt = coords.find('xs:altitud',namespace).text
    circuitCoords+=long+","
    circuitCoords+=lat+","
    circuitCoords+=alt
    newKml.addPlacemark("Salida","the start of the Shanghai's grand prix",long,lat,alt,'absolute')

    for coord in root.findall('.//xs:tramo/xs:coordenadas',namespace):
        circuitCoords+="\n"
        circuitCoords+=coord.find('xs:longitud',namespace).text
        circuitCoords+=","
        circuitCoords+=coord.find('xs:latitud',namespace).text
        circuitCoords+=","
        circuitCoords+=coord.find('xs:altitud',namespace).text

    newKml.addLineString("circuito","1","1",circuitCoords,'absolute','d14748ff',"10")
    

    outputName  = input("Introduzca el nombre del archivo generado (*.kml) = ")+".kml"

    newKml.writeKml(outputName)
    print("Creado el archivo: ", outputName)
   

if __name__ == "__main__":
    main()    
