/* --- DATOS COMPLETOS INTEGRADOS --- */
const tiposEquipos = {
  ENDOSCOPIA: [
    "Adaptador","Argón Plasma","Bomba de Irrigación","Bomba de Secreción","Bomba de CO2","Boquillas","Broncoscopio","Cable","Cable Bipolar","Cable Monopolar","Capturador de Video","Capuchón Distal","Carro","Cepillo de Limpieza","Colonoscopio","Conjunto de Irrigación","Contenedor de líquidos","Convertidor de Video","Duodenoscopio","Eliminador","Focos excelitas","Fuente de Luz","Gastroscopio","Grabador","Interfaz Monopolar para Erbe","Kit de Limpieza","Línea de Irrigación","Monitor","Mouse","Multicontacto","PC SIIMED Análogo","PC SIIMED HD","Pigtail","Pinzas de Endoscopia","Probador de Fuga","Procesador","Protector Bucal de Endoscopio","Protector de Punta de Endoscopio","Regulador de Argón de Endoscopia","Sistema Endoscopia","Tapon de Biopsia","Tapon-ETO","Tanque de Argón","Teclado","Valvúlas desechables","Valvúlas Reusables","Yugo Para Argón"
  ],
  LAPAROSCOPIA: [
    "Adaptador","Cabezal","Cable Interfaz 1688","Cable Interfaz USB 1588","Cámara","Case de Transporte","Charola de Esterilización","Clarity","Clips para Monitor","Fibra de Luz","Fuente de Luz","Grabador","Instrumental de Laparoscopia","Insuflador","Lente","Manguera de Insuflación","Manguera para Bomba de Agua","Monitor Grado Médico","Parche para Electrocauterio","Pedestal","Pieza de Mano","Pinza","Transmisor","Trocar","Video Carro","Video Grabador"
  ],
  QUIRÓFANO: [
    "Adaptador para Ligasure","Armónico Gen11","Bipap","Brazalete Pani","Bomba de Infusion","Cable Para Pinza Bipolar","Cable Trocal ECG","Carro para Electrocauterio","Carro Rojo Emergencias","Desfribilador","Electrocauterio","Eliminador","Fuente de Poder para Desfribilador","Lámpara de Quirófano","Lapíz para Electrocauterio","Ligasure LS8","Línea de Muestreo de CO2","Máquina de Anestesia","Mesa de Cirugía","Monitor Signos Vitales","Pedal Bipolar","Pedal Ligasure","Pedal Monopolar","Pieza de Mano Para Gen11","Placa para Electrocauterio","Sensor de ECG","Sensor de SPO2","Sensor PANI","Sensor de Temperatura","Vaporizador"
  ],
  HOSPITALIZACIÓN: [
    "Aspirador","Cama Hospitalaria Eléctrica","Camilla","Cuna Térmica","Incubadora","Mesa de Exploración","Ventilador"
  ],
  MATERIAL: [
    "Limpiador y Desengrasante"
  ],
  RADIOLOGÍA: ["Arco en C","Batería","Chasis","Flat Panel","Rayos X Rodable","Rayos X Portatil"],
  UROLOGÍA: ["Cistoscopio","Histeroscopio","Resectoscopio","Ureteroscopio Flexible", "Ureteroscopio Rigido"],
  ARTROSCOPIA: [
    "Batería","Cargador de Baterias","Camisa con Opturador","Cable para pedal","Cable para pieza de mano","Charola de Esterilización","Puntas de Radio Frecuencia","Endogia","Bomba de Irrigación","Pedal",
    "Lente",
    "Serfas de radiofrecuencia","Serfas Energy","Shaver","Rasurador", "Radio Frecuencia",
    "Set de Taladros de Artroscopia",
    "Transmisores",
    "Set de Cirugia Para Hombro y Tobillo", "Set de Cirugía de Rodilla",
    "Meditronic","linea de irrigacion"
  ],
  CEYE: ["Autoclave de cámara 95 L ","Monitor"],
  GINECOLOGÍA: ["Camilla Ginecologíca","Mesa de Exploración","Ultrasonido"]

};

const marcasModelosPorSubtipo = {
  laparoscopia: {
    'camara': {
      'Stryker': ['1188','1288','Precision','1488','1588','1688','1788'],
      'Karl Storz': ['IMAGE1 S', 'IMAGE1 HUB', 'Spies']
    },
    'insuflador': {
      'Stryker': ['High Flow 40L','PneumoSure 45L','PneumoClear 50L'],
      'Karl Storz': ['Endoflator 50', 'Endoflator 264320 20'],
    },
    'fuente de luz': {
      'Stryker': ['X8000', 'L9000', 'L10', 'L11'],
      'Karl Storz': ['Xenon 300', 'Power LED 300']
    },
    'monitor grado medico': {
      'Stryker': ['Vision Elect HDTV', 'VisionPro LED 26 Pulgadas', 'VisionPro SYNK LED 26 Pulgadas', '4K LED 32 Pulgadas', '4K 32 OLED Pulgadas', 'Wise HD 26 Pulgadas'],
    },
    'cabezal': {
      'Stryker': ['1188', '1288', 'Precision', '1488', '1588','1688', 'prueba']
    },
    'clarity': { 'Stryker': ['clarity'] },
    'grabador': { 'Stryker': ['SDC Ultra','SDC3','Connected OR HUB'] },
    'lente': {
      'Stryker': ['30-5mm Azul','30-5mm AIM','30-5mm Precision','30-10mm Precision','30-10mm AIM','30-10mm Azul']
    },
    'fibra de luz': {
      'Stryker' : ['X8000 Gris','L9000 Blanca','L10 y L11 Verde','Kit Ureteral IRIS']
    },
    'video carro': {
      'Stryker': ['Standar','Connected OR'],
    },
    'transmisor': {
      'Stryker': ['4K SYNK Wireless','4K SYNK Wireless Receiver','VisionPro SYNK Wireless','Wise HDTV Wireless']
    },
    'trocar': {
      'Ethicon': ['11mm X 100mm','12mm X 100mm 2D12-T'],
      'GM': ['KIT Trocares GYTR L KIT A','KIT TROCARES GYTR-LLL KIT A']
    },
    'pedestal': { 'Stryker': ['Pedestal'] },
    'instrumental de laparoscopia': { 
      'Ethicon': ['100mm x 12mm'],
      'GM': ['Aguja de Veress','Baja Nudos','Cable Bipolar','Cable monopolar','Clips Hemolok Dorado','Clips Hemolok Morado','Clips Hemolok Verde','Clips Titanio OC300','Clips Titanio OC400','Conjunto de Irrigacion y Succion desechable','Engrapadora Articulada','Engrapadora Hemolok Amarillo','Engrapadora Hemolok Dorado','Engrapadora Hemolok Morado','Engrapadora Hemolok Verde','Engrapadora Titanio LT300','Engrapadora Titanio LT400','Espatula','Gancho En L','Pinza Alligator','Pinza Babcock','Pinza Babcock Grasper 5mm 330mm','Pinza Babcock Grasper 10mm 330mm','Pinza Cobra','Pinza Colecistectomia','Pinza De Curva','Pinza De Disección','Pinza De Tijera Recta','Pinza Disectora','Pinza Extractora De Litos','Pinza Fenestrada','Pinza Grasper','Pinza Har23','Pinza Har26','Pinza Maryland Curva','Porta agujas 5mm 300mm','Retractor','Tijera Metzenbaum Doble Acción Curva 5mm* 330mm','Tubo de Irrigacion y Succion Reusable' ],
      'Covidien': ['Engrapadora Endogia Articulada 45mm Morado','Engrapadora Endogia Articulada 60mm Morado','Engrapadora Endogia Articulada 45mm Vascular Dorado','Engrapadora Endogia Articulada 60mm Vascular Dorado','Engrapadora Endogia ultra 12mm','Engrapadora Endoclip 10mm M/L','Engrapadora Tri-Staple Extra 60mm Negro'],
      'Storz': ['Pinza Grasper'],
    },
    'manguera de insuflacion': { 
      'stryker': ['manguera','yugo CO2']
    },
    'pinza': {
      'Covidien': ['Blunt Tip 5mm-37cm','Maryland 5mm-37cm','Maryland 5mm-23cm','Small Jam 16.5mm-19cm','Exact Dissector 20.6mm-21cm']
    },
    'adaptador': { 
      'stryker': ['Adaptador cople de lente','Adaptador frontal de Insuflador','Adaptador Trasero de Insuflador'],
    },
    'case de transporte': {
      'GM': [ 'Camara y Fuente L9000','Camara 1688 y Fuente L11','Grabador e Insuflador','Monitor Vision Pro led','Monitor 4K Stryker','Monitor 4K SONY']
    },
    'charola de esterilizacion': {
      'Stryker': ['Camara IAM','Lente de Laparoscopia'],
      'Storz': ['Lente de Laparoscopia'],
      'Artrhex': ['Lente de Laparoscopia'],
      'Olympus': ['Lente de Laparoscopia'],
    },
    'clips para monitor': {
      'GM':[ 'Porta Monitor']
    },
  },

  endoscopia: {
    'procesador': {
      'Olympus': ['CV-160','CV-170','CV-180','CV-190','EVIS X1'],
      'Fujifilm': ['VP-4400','VP-4440HD','EP-6000','EP-7000'],
      'Pentax': ['EPK-i','EPK-i7010'],
    },
    'fuente de luz': {
      'Olympus': ['CLV-160','CLV-180','CLV-190'],
      'Fujifilm': ['XL-4400','XL-4450','BL-7000'],
      'Pentax': ['Prueba']
    },
    'broncoscopio': { 'Olympus': ['BF-XP160F'] },
    'colonoscopio': {
      'Olympus': ['CF-Q160L','CF-H180AL','CF-HQ190L'],
      'Fujinon': ['EC-250HL5','EC-600HL','EC-760R-V/L'],
      'Pentax': ['EC-3890LI'],
    },
    'duodenoscopio': { 
      'Olympus': ['JF-140F','TJF-160F','TJF-160VF','TJF-Q180V','TJF-Q180','TJF-Q90V'],
      'Fujinon': ['ED-530XT'],
      'Pentax': ['ED-34-I10T2'],
    },
    'gastroscopio': {
      'Olympus': ['GIF-Q160','GIF-XP160','GIF-1TQ160','GIF-2T160','GIF-180','GIF-H180','GIF-H180J','GIF-HQ190'],
      'Fujinon': ['EG-530N','EG-530WR','EG-600WR','EG-6400N','EG-760R'],
      'Pentax': ['EG-2990i'],
    },
    'argon plasma': { 'Erbe': ['ICC200','ICC300','VIO 300D','APC300', 'APC'] },
    'bomba de co2': { 'Fujinon': ['GW-100'] },
    'bomba de irrigacion': {
      'Olympus': ['UCR','OFP','OFP2'],
      'Medivators': ['Endogator EGP-100','Stratus EGA-500'],
      'Erbe': ['EIP 2'],
    },
    'bomba de secrecion': { 'Infusomat': ['Braun Sumalfit'] },
    'capturador de video': { 'Ugreen': ['HDMI'] },
    'convertidor de video': { 'GM': ['X003'] },
    'monitor': {
      'Olympus': ['OEV 262H','OEV 191H'],
      'Storz': ['4k 32"','Led 26"'],
      'Sony': ['HD 19"','4k 55"'],
    },
    'Adaptador': {
      'Valleylab': ['Adapatador Bipolar Azul Active Only'],
      'Erbe': ['Adaptador Bipolar ICC 200 ','Adaptador para Sonda ICC200 ICC300 VIO 300D','Sonda Circular'],
      'Generico': ['Adaptador para el canal de Biopsia']
    },
    'grabador': { 'KingMa': ['KM-YK980'] },
    'interfaz monopolar para erbe': { 'Erbe': ['Cable interfaz'] },
    'eliminador': { 
      'Storz': ['4k 32"','Led 26"'],
      'Sony': ['HD 19"','4k 55"'],
    },
    'focos excelitas': {
      'PE300BFA': ['180-160-4400-4450-Xenon300'],
      'PE150AF': ['Fujinon-2200'],
      'Y1911': ['EPK-5010','EPKI-7010'],
      'Y1882': ['EPK-i'],
      'Y1964': ['EPK-5010','EPKI-7010'],
    },
    'Carro': { 
      'Olympus': ['Para sistema 160 o 180','Para sistema 190'],
      'Fujinon': ['Carro Original'],
      'GM':['Carro GM'],
    },
    'kit de limpieza': { 
      'Olympus': ['MH-946 para 160 180 y 190'],
      'Fujinon': ['WA-007 para 760'],
    },
    'linea de irrigacion': {
      'GM': ['Genericas'],
      'Medivators': ['OFP','OFP 2','Stratus'],
    },
    'contenedor de liquidos': {
      'Olympus': ['Serie 100','160','180','190'],
      'Fujinon': [ 'Serie 500 y 600','760','760 para Insuflador'],
      'Pentax': ['Serie 7010'],
    },
    'Pinzas de Endoscopia': {
      'Olympus': ['pinza de biopsia','pinza de biopsia hot','pinza de canasta','pinza de 4 hilos','pinza de extraccion','pinza de polipectomia'],
      'GM': ['Prueba']
    },
    'probador de fuga': { 
      'Olympus': ['Serie 160 180 190'],
      'Fujinon': [ 'Serie 500 y 600','Serie 760'],
      'Pentax': ['Serie 90i'],
    },
    'protector bucal de endoscopio': { 'Olympus': ['MB-142 Olympus'] },
    'protector de punta de endoscopio': { 'GM': ['Protector Azul'] },
    'tapon de biopsia': { 'GM': ['GM'] },
    'tapon-eto': { 'Olympus': ['MH-553'] },
    'Tanque de Argón':{'GM':['Tanque de Argón'] },
    'valvulas desechables': { },
    'valvulas reusables': { 'Fujinon': [ 'Serie 760'] },
    'yugo para argon': { 'Erbe': ['ICC200','ICC300','VIO 300D','APC300', 'APC'] },
    'teclado': { 
      'Olympus': ['Serie 100','160','180','190'],
      'Fujinon': [ 'Serie 500 y 600','760'],
      'Pentax': ['Serie 7010'],
    },
    'mouse': { 'GM': ['GM'] },
    'multicontacto': { },
    'pc siimed analogo': { },
    'pc siimed hd': { },
    'pigtail': { 'Olympus': ['Maj-1430'] },
    'cable': { },
    'cable bipolar': { },
    'cable monopolar': { },
    'boquillas': { },
    'cepillo de limpieza': { },
    'capuchon distal': { },
  },

  quirofano: {
    'adaptador para ligasure': {
      'Cad':['LS8','Force FX','Force 2','Adaptador Bipolar LS8']
    },
    'ligasure ls8': { 'Medtronic': ['LS8'] },
    'electrocauterio': {
      'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
      'Erbe': ['ICC 200','ICC 300','VIO 300D'],
      'Olympus': ['ESG-400',],
      'GM': ['CITADEL 300'],
      'Conmed': ['Sabre Genesis'],
    },
    'brazalete pani': { 
      'Datex-Ohmeda': ['Cardiocap5'],
      'Drager': ['Delta Infinity'],
      'Phillips': ['MP50 Intellivue','MP70 Intellivue'],
      'Mindray': ['V12'],
    },
    'Bomba de Infusion': {
      'Dre Med':[ 'NTx3 Plus'],
    },
    'maquina de anestesia': {
      'Datex-Ohmeda': ['Aestiva','Avance','Aisys','Aespire'],
      'Dräger':['Fabius MRI'],
    },
    'mesa de cirugia': {
      'Amsco': ['2080 Semielectrica y SemiTraslucida' ,'3080 Electrica y Traslucida'],
      'Maquet':['AlphaStart']
    },
    'lampara de quirofano': {
      'Stryker': ['Vision 2'],
      'Skytron': ['Aurora'],
    },
    'monitor signos vitales': {
      'Datex-Ohmeda': ['Cardiocap5'],
      'Drager': ['Delta Infinity'],
      'Phillips': ['MP50 Intellivue','MP70 Intellivue'],
      'Mindray': ['V12'],
    },
    'desfribilador': {
      'Phillips': ['Heartstart MRX'],
      'Zoll': ['AED plus'],
    },
    'bipap': {
      'Phillips':['Ventilador Respironics Nuevo']
    },
    'vaporizador': { 
      'Datex-Ohmeda': ['Tec 7 Aestiva-Aespire','Casette Aisys'],
    },
    'sensor de ecg': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda':['Cardiocap5'],
      'Mindray': ['V12']
    },
    'sensor de spo2': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda':['Cardiocap5'],
      'Mindray': ['V12']
    },
    'sensor pani': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda':['Cardiocap5'],
      'Mindray': ['V12']
    },
    'sensor de temperatura': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda':['Cardiocap5'],
      'Mindray': ['V12']
    },
    'pedal bipolar': {
      'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
      'Conmed': ['Sabre Genesis'],
    },
    'pedal monopolar': { 
      'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
      'Conmed': ['Sabre Genesis'],
      'Olympus': ['ESG-400']
    },
    'pedal ligasure': { 
      'Covidien':[ 'Pedal Bipolar Morado','Pedal Bipolar Anaranjado']
    },
    'placa para electrocauterio': {
      'OBS':['Placa desechable']
    },
    'lapiz para electrocauterio': {
      'Avante':['Placa desechable'],
      'OBS':['Placa desechable'],
      'Covidien': [ 'Placa desechable'],
      'Conmed':['Placa desechable'],
      'Smith&Nephew':['Placa desechable']
    },
    'Línea de Muestreo de CO2': {
      'Datex Ohmeda':['Aisys','Avance' ,'Cardiocap5'],
      'Phillips': ['Heartstart MRX'],
    },
    'cable para pinza bipolar': {
      'Covidien': [ 'Pinza Bipolar']
    },
    'cable trocal ecg': { 
      'Drager': ['Delta Infinity'],
    },
    'carro para electrocauterio': { 
      'Erbe': [ 'Para ERBE'],
      'Covidien': ['Force 2','Force FX','ForceTriad','FT10'],
    },
    'carro rojo Emergencias': {
      'Lifeline': [ 'Carro de Emergencias'],
      'GM': [ 'Carro de Emergencias NUEVO'],
    },
    'eliminador': {
      'Phillips': ['Fuente de poder Desfibrilador MRX']
    },
    'pieza de mano para gen11': {
      'Ethicon':[ 'Pieza con 4 usos','Pieza con 70 usos','Pieza con 87 usos']
    },
    'armonico gen11': { 
      'Ethicon':[ 'Armonico GEN11']
    },
  },

  hospitalizacion: {
    'aspirador': {
      'Hergon': ['7E-A NUEVO']
    },
    'cama hospitalaria electrica': {
      'Hill Roon':['Versacare',],
      'stryker':['MPS Secure II','S3']
    },
    'camilla': { 
      'Hill Roon':['P8000'],
      'Stryker':['Prime Series','1015 Stretcher'],
    },
    'cuna termica': {
      'GE Healthcare':[' Panda Warmer']
    },
    'incubadora': {
      'GE': [' Giraffe'],
    },
    'mesa de exploracion': { },
    'ventilador': {
      'Nellcor': ['Puritan Benett 840']
    },
  },

  radiologia: {
    'arco en c': { },
    'bateria': { },
    'chasis': { },
    'flat panel': { },
    'rayos x rodable': { },
    'rayos x portatil': { },
  },

  urologia: {
    'cistoscopio': { },
    'histeroscopio': { },
    'resectoscopio': { },
    'ureteroscopio flexible': { },
    'ureteroscopio rigido': { },
  },

  artroscopia: {
    'shaver': { },
    'rasurador': { },
    'radio frecuencia': { },
    'puntas de radio frecuencia': {
      'Stryker': ['Cortadora Agresiva Plus 3.5mm x 80mm Amarillo','Cortadora Agresiva Plus 5.0mm x 125mm Azul','Cortadora Angular 4.0mm x 125mm Rojo','Cortadora Angular 5.0mm x 125mm Azul','Cortadora Resector 3.5mm x 125mm Amarillo','Cortadora XL Agresiva 4.0mm x 180mm Rojo','Fresa 5mm x 125mm Azul','Fresa de Abrasion 2.0mm x 80mm Morado','Fresa Redonda de 12 filos 5.5mm x 125mm Café','Fresa de Barril de 12 hilos 5.5mm x 125mm Cafe'],
    },
    'serfas de radiofrecuencia': { },
    'serfas energy': { },
    'bomba de irrigacion': { },
    'lente': { 'Stryker': ['30-4mm'] },
    'transmisores': { },
    'pedal': {
      'Arthocare': ['Coblator II']
    },
    'set de taladros de artroscopia': {
      'Stryker': [ 'System 7 Mandril llave']
    },
    'camisa con opturador': { },
    'cable para pedal': { },
    'cable para pieza de mano': { },
    'charola de esterilizacion': {
      'Stryker': ['Art-Stryker']
    },
    'bateria': { },
    'cargador de baterias': { 
      'Stryker': ['Taladros'],
    },
    'meditronic': { },
    'set de cirugia para hombro y tobillo': { },
    'set de cirugia de rodilla': { },
  },

  ceye: {
    'autoclave de camara 95 l': { },
    'monitor': { },
  },

  ginecologia: {
    'Camilla Ginecologíca': { 
      'Stryker': ['Geynnie'],
    },
    'Ultrasonido': {
      'GE': [ 'Logic P3'],
    },
    'mesa de exploracion': { 
      'Midmark': ['Modelo 404',' Ritte 622']
    },
  },

  material: {
    'Limpiador y Desengrasante': {
      'Steren': ['Desengrasante Y Limpiador']
    },

  },

};