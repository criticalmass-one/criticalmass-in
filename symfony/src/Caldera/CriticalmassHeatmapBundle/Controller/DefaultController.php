<?php

namespace Caldera\CriticalmassHeatmapBundle\Controller;

use Caldera\CriticalmassHeatmapBundle\Utility\GPXConverter;
use Caldera\CriticalmassHeatmapBundle\Utility\Path;
use Caldera\CriticalmassHeatmapBundle\Utility\PNGTilePrinter;
use Caldera\CriticalmassHeatmapBundle\Utility\Position;
use Caldera\CriticalmassHeatmapBundle\Utility\Tile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $gpx = '<?xml version="1.0" encoding="UTF-8"?>
<gpx creator="strava.com iPhone" version="1.1" xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">
 <metadata>
  <time>2014-04-26T07:01:21Z</time>
 </metadata>
 <trk>
  <name>Frühstück</name>
  <trkseg>
   <trkpt lat="54.3154050" lon="10.1315380">
    <ele>9.3</ele>
    <time>2014-04-26T07:01:21Z</time>
   </trkpt>
   <trkpt lat="54.3152930" lon="10.1311060">
    <ele>10.0</ele>
    <time>2014-04-26T07:01:23Z</time>
   </trkpt>
   <trkpt lat="54.3152100" lon="10.1307860">
    <ele>10.2</ele>
    <time>2014-04-26T07:01:24Z</time>
   </trkpt>
   <trkpt lat="54.3151760" lon="10.1307370">
    <ele>10.1</ele>
    <time>2014-04-26T07:01:25Z</time>
   </trkpt>
   <trkpt lat="54.3151520" lon="10.1308080">
    <ele>10.0</ele>
    <time>2014-04-26T07:01:27Z</time>
   </trkpt>
   <trkpt lat="54.3152110" lon="10.1308270">
    <ele>10.1</ele>
    <time>2014-04-26T07:01:28Z</time>
   </trkpt>
   <trkpt lat="54.3152430" lon="10.1310660">
    <ele>10.0</ele>
    <time>2014-04-26T07:01:29Z</time>
   </trkpt>
   <trkpt lat="54.3152870" lon="10.1313960">
    <ele>9.5</ele>
    <time>2014-04-26T07:01:30Z</time>
   </trkpt>
   <trkpt lat="54.3152620" lon="10.1313730">
    <ele>9.5</ele>
    <time>2014-04-26T07:01:31Z</time>
   </trkpt>
   <trkpt lat="54.3152770" lon="10.1313110">
    <ele>9.7</ele>
    <time>2014-04-26T07:01:43Z</time>
   </trkpt>
   <trkpt lat="54.3152890" lon="10.1312570">
    <ele>9.8</ele>
    <time>2014-04-26T07:06:33Z</time>
   </trkpt>
   <trkpt lat="54.3152620" lon="10.1312440">
    <ele>9.8</ele>
    <time>2014-04-26T07:06:37Z</time>
   </trkpt>
   <trkpt lat="54.3152190" lon="10.1312610">
    <ele>9.7</ele>
    <time>2014-04-26T07:06:43Z</time>
   </trkpt>
   <trkpt lat="54.3151830" lon="10.1312530">
    <ele>9.7</ele>
    <time>2014-04-26T07:06:46Z</time>
   </trkpt>
   <trkpt lat="54.3151560" lon="10.1312510">
    <ele>9.7</ele>
    <time>2014-04-26T07:06:48Z</time>
   </trkpt>
   <trkpt lat="54.3151290" lon="10.1312460">
    <ele>9.7</ele>
    <time>2014-04-26T07:06:50Z</time>
   </trkpt>
   <trkpt lat="54.3150930" lon="10.1312340">
    <ele>9.6</ele>
    <time>2014-04-26T07:06:53Z</time>
   </trkpt>
   <trkpt lat="54.3150570" lon="10.1312260">
    <ele>9.6</ele>
    <time>2014-04-26T07:06:56Z</time>
   </trkpt>
   <trkpt lat="54.3150180" lon="10.1312110">
    <ele>9.5</ele>
    <time>2014-04-26T07:06:59Z</time>
   </trkpt>
   <trkpt lat="54.3149910" lon="10.1312020">
    <ele>9.5</ele>
    <time>2014-04-26T07:07:01Z</time>
   </trkpt>
   <trkpt lat="54.3149530" lon="10.1311870">
    <ele>9.5</ele>
    <time>2014-04-26T07:07:04Z</time>
   </trkpt>
   <trkpt lat="54.3149160" lon="10.1311720">
    <ele>9.4</ele>
    <time>2014-04-26T07:07:07Z</time>
   </trkpt>
   <trkpt lat="54.3148790" lon="10.1311520">
    <ele>9.4</ele>
    <time>2014-04-26T07:07:10Z</time>
   </trkpt>
   <trkpt lat="54.3148510" lon="10.1311250">
    <ele>9.4</ele>
    <time>2014-04-26T07:07:13Z</time>
   </trkpt>
   <trkpt lat="54.3148300" lon="10.1310930">
    <ele>9.4</ele>
    <time>2014-04-26T07:07:16Z</time>
   </trkpt>
   <trkpt lat="54.3148180" lon="10.1310420">
    <ele>9.4</ele>
    <time>2014-04-26T07:07:19Z</time>
   </trkpt>
   <trkpt lat="54.3148340" lon="10.1309710">
    <ele>9.4</ele>
    <time>2014-04-26T07:07:21Z</time>
   </trkpt>
   <trkpt lat="54.3148450" lon="10.1308870">
    <ele>9.5</ele>
    <time>2014-04-26T07:07:23Z</time>
   </trkpt>
   <trkpt lat="54.3148520" lon="10.1308390">
    <ele>9.5</ele>
    <time>2014-04-26T07:07:24Z</time>
   </trkpt>
   <trkpt lat="54.3148590" lon="10.1307880">
    <ele>9.5</ele>
    <time>2014-04-26T07:07:25Z</time>
   </trkpt>
   <trkpt lat="54.3148680" lon="10.1307280">
    <ele>9.6</ele>
    <time>2014-04-26T07:07:26Z</time>
   </trkpt>
   <trkpt lat="54.3148800" lon="10.1306630">
    <ele>9.6</ele>
    <time>2014-04-26T07:07:27Z</time>
   </trkpt>
   <trkpt lat="54.3148930" lon="10.1305980">
    <ele>9.7</ele>
    <time>2014-04-26T07:07:28Z</time>
   </trkpt>
   <trkpt lat="54.3149050" lon="10.1305330">
    <ele>9.7</ele>
    <time>2014-04-26T07:07:29Z</time>
   </trkpt>
   <trkpt lat="54.3149210" lon="10.1304760">
    <ele>9.8</ele>
    <time>2014-04-26T07:07:30Z</time>
   </trkpt>
   <trkpt lat="54.3149350" lon="10.1304190">
    <ele>9.8</ele>
    <time>2014-04-26T07:07:31Z</time>
   </trkpt>
   <trkpt lat="54.3149500" lon="10.1303640">
    <ele>9.8</ele>
    <time>2014-04-26T07:07:32Z</time>
   </trkpt>
   <trkpt lat="54.3149630" lon="10.1303130">
    <ele>9.8</ele>
    <time>2014-04-26T07:07:33Z</time>
   </trkpt>
   <trkpt lat="54.3149780" lon="10.1302510">
    <ele>9.8</ele>
    <time>2014-04-26T07:07:34Z</time>
   </trkpt>
   <trkpt lat="54.3149890" lon="10.1301970">
    <ele>9.8</ele>
    <time>2014-04-26T07:07:35Z</time>
   </trkpt>
   <trkpt lat="54.3150090" lon="10.1301380">
    <ele>9.9</ele>
    <time>2014-04-26T07:07:36Z</time>
   </trkpt>
   <trkpt lat="54.3150260" lon="10.1300840">
    <ele>9.9</ele>
    <time>2014-04-26T07:07:37Z</time>
   </trkpt>
   <trkpt lat="54.3150420" lon="10.1300330">
    <ele>9.9</ele>
    <time>2014-04-26T07:07:38Z</time>
   </trkpt>
   <trkpt lat="54.3150600" lon="10.1299740">
    <ele>9.9</ele>
    <time>2014-04-26T07:07:39Z</time>
   </trkpt>
   <trkpt lat="54.3150760" lon="10.1299150">
    <ele>9.9</ele>
    <time>2014-04-26T07:07:40Z</time>
   </trkpt>
   <trkpt lat="54.3150900" lon="10.1298570">
    <ele>10.0</ele>
    <time>2014-04-26T07:07:41Z</time>
   </trkpt>
   <trkpt lat="54.3151020" lon="10.1298020">
    <ele>10.0</ele>
    <time>2014-04-26T07:07:42Z</time>
   </trkpt>
   <trkpt lat="54.3151170" lon="10.1297470">
    <ele>10.0</ele>
    <time>2014-04-26T07:07:43Z</time>
   </trkpt>
   <trkpt lat="54.3151320" lon="10.1296970">
    <ele>10.0</ele>
    <time>2014-04-26T07:07:44Z</time>
   </trkpt>
   <trkpt lat="54.3151480" lon="10.1296460">
    <ele>10.1</ele>
    <time>2014-04-26T07:07:45Z</time>
   </trkpt>
   <trkpt lat="54.3151620" lon="10.1295920">
    <ele>10.1</ele>
    <time>2014-04-26T07:07:46Z</time>
   </trkpt>
   <trkpt lat="54.3151740" lon="10.1295380">
    <ele>10.1</ele>
    <time>2014-04-26T07:07:47Z</time>
   </trkpt>
   <trkpt lat="54.3151880" lon="10.1294920">
    <ele>10.2</ele>
    <time>2014-04-26T07:07:48Z</time>
   </trkpt>
   <trkpt lat="54.3152020" lon="10.1294450">
    <ele>10.2</ele>
    <time>2014-04-26T07:07:49Z</time>
   </trkpt>
   <trkpt lat="54.3152160" lon="10.1293980">
    <ele>10.3</ele>
    <time>2014-04-26T07:07:50Z</time>
   </trkpt>
   <trkpt lat="54.3152280" lon="10.1293540">
    <ele>10.3</ele>
    <time>2014-04-26T07:07:51Z</time>
   </trkpt>
   <trkpt lat="54.3152420" lon="10.1293030">
    <ele>10.4</ele>
    <time>2014-04-26T07:07:52Z</time>
   </trkpt>
   <trkpt lat="54.3152590" lon="10.1292160">
    <ele>10.4</ele>
    <time>2014-04-26T07:07:54Z</time>
   </trkpt>
   <trkpt lat="54.3152730" lon="10.1291740">
    <ele>10.5</ele>
    <time>2014-04-26T07:07:55Z</time>
   </trkpt>
   <trkpt lat="54.3152890" lon="10.1291350">
    <ele>10.5</ele>
    <time>2014-04-26T07:07:56Z</time>
   </trkpt>
   <trkpt lat="54.3153060" lon="10.1290900">
    <ele>10.6</ele>
    <time>2014-04-26T07:07:57Z</time>
   </trkpt>
   <trkpt lat="54.3153270" lon="10.1290130">
    <ele>10.7</ele>
    <time>2014-04-26T07:07:59Z</time>
   </trkpt>
   <trkpt lat="54.3153360" lon="10.1289690">
    <ele>10.7</ele>
    <time>2014-04-26T07:08:00Z</time>
   </trkpt>
   <trkpt lat="54.3153560" lon="10.1288770">
    <ele>10.8</ele>
    <time>2014-04-26T07:08:02Z</time>
   </trkpt>
   <trkpt lat="54.3153790" lon="10.1288240">
    <ele>10.9</ele>
    <time>2014-04-26T07:08:04Z</time>
   </trkpt>
   <trkpt lat="54.3153960" lon="10.1287770">
    <ele>10.9</ele>
    <time>2014-04-26T07:08:06Z</time>
   </trkpt>
   <trkpt lat="54.3153530" lon="10.1287580">
    <ele>10.8</ele>
    <time>2014-04-26T07:08:15Z</time>
   </trkpt>
   <trkpt lat="54.3153620" lon="10.1287090">
    <ele>10.9</ele>
    <time>2014-04-26T07:08:36Z</time>
   </trkpt>
   <trkpt lat="54.3153810" lon="10.1286450">
    <ele>11.0</ele>
    <time>2014-04-26T07:08:38Z</time>
   </trkpt>
   <trkpt lat="54.3153950" lon="10.1286040">
    <ele>11.1</ele>
    <time>2014-04-26T07:08:39Z</time>
   </trkpt>
   <trkpt lat="54.3154100" lon="10.1285530">
    <ele>11.2</ele>
    <time>2014-04-26T07:08:40Z</time>
   </trkpt>
   <trkpt lat="54.3154270" lon="10.1285100">
    <ele>11.3</ele>
    <time>2014-04-26T07:08:41Z</time>
   </trkpt>
   <trkpt lat="54.3154390" lon="10.1284650">
    <ele>11.4</ele>
    <time>2014-04-26T07:08:42Z</time>
   </trkpt>
   <trkpt lat="54.3154500" lon="10.1283870">
    <ele>11.5</ele>
    <time>2014-04-26T07:08:44Z</time>
   </trkpt>
   <trkpt lat="54.3154720" lon="10.1283160">
    <ele>11.6</ele>
    <time>2014-04-26T07:08:47Z</time>
   </trkpt>
   <trkpt lat="54.3154920" lon="10.1282150">
    <ele>11.7</ele>
    <time>2014-04-26T07:08:49Z</time>
   </trkpt>
   <trkpt lat="54.3155260" lon="10.1281110">
    <ele>11.9</ele>
    <time>2014-04-26T07:08:50Z</time>
   </trkpt>
   <trkpt lat="54.3155380" lon="10.1280380">
    <ele>12.0</ele>
    <time>2014-04-26T07:08:51Z</time>
   </trkpt>
   <trkpt lat="54.3155540" lon="10.1279790">
    <ele>12.1</ele>
    <time>2014-04-26T07:08:52Z</time>
   </trkpt>
   <trkpt lat="54.3155690" lon="10.1279180">
    <ele>12.2</ele>
    <time>2014-04-26T07:08:53Z</time>
   </trkpt>
   <trkpt lat="54.3155830" lon="10.1278550">
    <ele>12.2</ele>
    <time>2014-04-26T07:08:54Z</time>
   </trkpt>
   <trkpt lat="54.3155980" lon="10.1277940">
    <ele>12.2</ele>
    <time>2014-04-26T07:08:55Z</time>
   </trkpt>
   <trkpt lat="54.3156120" lon="10.1277280">
    <ele>12.2</ele>
    <time>2014-04-26T07:08:56Z</time>
   </trkpt>
   <trkpt lat="54.3156260" lon="10.1276710">
    <ele>12.3</ele>
    <time>2014-04-26T07:08:57Z</time>
   </trkpt>
   <trkpt lat="54.3156390" lon="10.1275980">
    <ele>12.3</ele>
    <time>2014-04-26T07:08:58Z</time>
   </trkpt>
   <trkpt lat="54.3156520" lon="10.1275300">
    <ele>12.3</ele>
    <time>2014-04-26T07:08:59Z</time>
   </trkpt>
   <trkpt lat="54.3156680" lon="10.1274680">
    <ele>12.3</ele>
    <time>2014-04-26T07:09:00Z</time>
   </trkpt>
   <trkpt lat="54.3156840" lon="10.1274020">
    <ele>12.3</ele>
    <time>2014-04-26T07:09:01Z</time>
   </trkpt>
   <trkpt lat="54.3156990" lon="10.1273370">
    <ele>12.3</ele>
    <time>2014-04-26T07:09:02Z</time>
   </trkpt>
   <trkpt lat="54.3157140" lon="10.1272700">
    <ele>12.4</ele>
    <time>2014-04-26T07:09:03Z</time>
   </trkpt>
   <trkpt lat="54.3157310" lon="10.1272040">
    <ele>12.4</ele>
    <time>2014-04-26T07:09:04Z</time>
   </trkpt>
   <trkpt lat="54.3157480" lon="10.1271380">
    <ele>12.4</ele>
    <time>2014-04-26T07:09:05Z</time>
   </trkpt>
   <trkpt lat="54.3157650" lon="10.1270710">
    <ele>12.5</ele>
    <time>2014-04-26T07:09:06Z</time>
   </trkpt>
   <trkpt lat="54.3157820" lon="10.1270040">
    <ele>12.8</ele>
    <time>2014-04-26T07:09:07Z</time>
   </trkpt>
   <trkpt lat="54.3158010" lon="10.1269420">
    <ele>13.1</ele>
    <time>2014-04-26T07:09:08Z</time>
   </trkpt>
   <trkpt lat="54.3158260" lon="10.1268910">
    <ele>13.3</ele>
    <time>2014-04-26T07:09:09Z</time>
   </trkpt>
   <trkpt lat="54.3158450" lon="10.1268360">
    <ele>13.5</ele>
    <time>2014-04-26T07:09:10Z</time>
   </trkpt>
   <trkpt lat="54.3158580" lon="10.1267760">
    <ele>13.8</ele>
    <time>2014-04-26T07:09:11Z</time>
   </trkpt>
   <trkpt lat="54.3158750" lon="10.1267300">
    <ele>14.0</ele>
    <time>2014-04-26T07:09:12Z</time>
   </trkpt>
   <trkpt lat="54.3158920" lon="10.1266830">
    <ele>14.2</ele>
    <time>2014-04-26T07:09:13Z</time>
   </trkpt>
   <trkpt lat="54.3159100" lon="10.1266400">
    <ele>14.4</ele>
    <time>2014-04-26T07:09:14Z</time>
   </trkpt>
   <trkpt lat="54.3159340" lon="10.1265630">
    <ele>14.7</ele>
    <time>2014-04-26T07:09:16Z</time>
   </trkpt>
   <trkpt lat="54.3159450" lon="10.1265170">
    <ele>14.9</ele>
    <time>2014-04-26T07:09:17Z</time>
   </trkpt>
   <trkpt lat="54.3159860" lon="10.1264530">
    <ele>15.2</ele>
    <time>2014-04-26T07:09:19Z</time>
   </trkpt>
   <trkpt lat="54.3160090" lon="10.1264220">
    <ele>15.3</ele>
    <time>2014-04-26T07:09:20Z</time>
   </trkpt>
   <trkpt lat="54.3160320" lon="10.1263880">
    <ele>15.5</ele>
    <time>2014-04-26T07:09:21Z</time>
   </trkpt>
   <trkpt lat="54.3160550" lon="10.1263520">
    <ele>15.6</ele>
    <time>2014-04-26T07:09:22Z</time>
   </trkpt>
   <trkpt lat="54.3160780" lon="10.1263080">
    <ele>15.8</ele>
    <time>2014-04-26T07:09:23Z</time>
   </trkpt>
   <trkpt lat="54.3161010" lon="10.1262830">
    <ele>15.9</ele>
    <time>2014-04-26T07:09:24Z</time>
   </trkpt>
   <trkpt lat="54.3161240" lon="10.1262410">
    <ele>16.0</ele>
    <time>2014-04-26T07:09:25Z</time>
   </trkpt>
   <trkpt lat="54.3161580" lon="10.1261710">
    <ele>16.3</ele>
    <time>2014-04-26T07:09:27Z</time>
   </trkpt>
   <trkpt lat="54.3161770" lon="10.1261320">
    <ele>16.4</ele>
    <time>2014-04-26T07:09:28Z</time>
   </trkpt>
   <trkpt lat="54.3161970" lon="10.1260870">
    <ele>16.6</ele>
    <time>2014-04-26T07:09:29Z</time>
   </trkpt>
   <trkpt lat="54.3162370" lon="10.1260320">
    <ele>16.8</ele>
    <time>2014-04-26T07:09:31Z</time>
   </trkpt>
   <trkpt lat="54.3162770" lon="10.1259710">
    <ele>17.0</ele>
    <time>2014-04-26T07:09:33Z</time>
   </trkpt>
   <trkpt lat="54.3162970" lon="10.1259390">
    <ele>17.2</ele>
    <time>2014-04-26T07:09:34Z</time>
   </trkpt>
   <trkpt lat="54.3163180" lon="10.1259040">
    <ele>17.3</ele>
    <time>2014-04-26T07:09:35Z</time>
   </trkpt>
   <trkpt lat="54.3163390" lon="10.1258670">
    <ele>17.4</ele>
    <time>2014-04-26T07:09:36Z</time>
   </trkpt>
   <trkpt lat="54.3163620" lon="10.1258280">
    <ele>17.6</ele>
    <time>2014-04-26T07:09:37Z</time>
   </trkpt>
   <trkpt lat="54.3163850" lon="10.1257870">
    <ele>17.7</ele>
    <time>2014-04-26T07:09:38Z</time>
   </trkpt>
   <trkpt lat="54.3164100" lon="10.1257480">
    <ele>17.9</ele>
    <time>2014-04-26T07:09:39Z</time>
   </trkpt>
   <trkpt lat="54.3164350" lon="10.1257080">
    <ele>18.0</ele>
    <time>2014-04-26T07:09:40Z</time>
   </trkpt>
   <trkpt lat="54.3164600" lon="10.1256660">
    <ele>18.2</ele>
    <time>2014-04-26T07:09:41Z</time>
   </trkpt>
   <trkpt lat="54.3164850" lon="10.1256200">
    <ele>18.3</ele>
    <time>2014-04-26T07:09:42Z</time>
   </trkpt>
   <trkpt lat="54.3165080" lon="10.1255710">
    <ele>18.5</ele>
    <time>2014-04-26T07:09:43Z</time>
   </trkpt>
   <trkpt lat="54.3165300" lon="10.1255270">
    <ele>18.6</ele>
    <time>2014-04-26T07:09:44Z</time>
   </trkpt>
   <trkpt lat="54.3165500" lon="10.1254860">
    <ele>18.8</ele>
    <time>2014-04-26T07:09:45Z</time>
   </trkpt>
   <trkpt lat="54.3165580" lon="10.1254340">
    <ele>18.9</ele>
    <time>2014-04-26T07:09:46Z</time>
   </trkpt>
   <trkpt lat="54.3165640" lon="10.1253800">
    <ele>19.1</ele>
    <time>2014-04-26T07:09:47Z</time>
   </trkpt>
   <trkpt lat="54.3165600" lon="10.1253220">
    <ele>19.3</ele>
    <time>2014-04-26T07:09:48Z</time>
   </trkpt>
   <trkpt lat="54.3165530" lon="10.1252690">
    <ele>19.4</ele>
    <time>2014-04-26T07:09:49Z</time>
   </trkpt>
   <trkpt lat="54.3165390" lon="10.1252280">
    <ele>19.5</ele>
    <time>2014-04-26T07:09:50Z</time>
   </trkpt>
   <trkpt lat="54.3165220" lon="10.1251830">
    <ele>19.7</ele>
    <time>2014-04-26T07:09:51Z</time>
   </trkpt>
   <trkpt lat="54.3164910" lon="10.1251030">
    <ele>19.9</ele>
    <time>2014-04-26T07:09:53Z</time>
   </trkpt>
   <trkpt lat="54.3164740" lon="10.1250610">
    <ele>20.1</ele>
    <time>2014-04-26T07:09:54Z</time>
   </trkpt>
   <trkpt lat="54.3164570" lon="10.1250220">
    <ele>20.2</ele>
    <time>2014-04-26T07:09:55Z</time>
   </trkpt>
   <trkpt lat="54.3164380" lon="10.1249860">
    <ele>20.3</ele>
    <time>2014-04-26T07:09:57Z</time>
   </trkpt>
   <trkpt lat="54.3163970" lon="10.1249180">
    <ele>20.6</ele>
    <time>2014-04-26T07:09:59Z</time>
   </trkpt>
   <trkpt lat="54.3163590" lon="10.1248490">
    <ele>20.9</ele>
    <time>2014-04-26T07:10:00Z</time>
   </trkpt>
   <trkpt lat="54.3163170" lon="10.1247810">
    <ele>21.2</ele>
    <time>2014-04-26T07:10:02Z</time>
   </trkpt>
   <trkpt lat="54.3162710" lon="10.1247240">
    <ele>21.5</ele>
    <time>2014-04-26T07:10:04Z</time>
   </trkpt>
   <trkpt lat="54.3162480" lon="10.1246960">
    <ele>21.6</ele>
    <time>2014-04-26T07:10:05Z</time>
   </trkpt>
   <trkpt lat="54.3162220" lon="10.1246710">
    <ele>21.7</ele>
    <time>2014-04-26T07:10:06Z</time>
   </trkpt>
   <trkpt lat="54.3161960" lon="10.1246410">
    <ele>21.9</ele>
    <time>2014-04-26T07:10:07Z</time>
   </trkpt>
   <trkpt lat="54.3161720" lon="10.1246120">
    <ele>22.1</ele>
    <time>2014-04-26T07:10:08Z</time>
   </trkpt>
   <trkpt lat="54.3161470" lon="10.1245810">
    <ele>22.3</ele>
    <time>2014-04-26T07:10:09Z</time>
   </trkpt>
   <trkpt lat="54.3161300" lon="10.1245450">
    <ele>22.4</ele>
    <time>2014-04-26T07:10:10Z</time>
   </trkpt>
   <trkpt lat="54.3161110" lon="10.1245090">
    <ele>22.6</ele>
    <time>2014-04-26T07:10:11Z</time>
   </trkpt>
   <trkpt lat="54.3160910" lon="10.1244740">
    <ele>22.8</ele>
    <time>2014-04-26T07:10:12Z</time>
   </trkpt>
   <trkpt lat="54.3160720" lon="10.1244360">
    <ele>23.0</ele>
    <time>2014-04-26T07:10:13Z</time>
   </trkpt>
   <trkpt lat="54.3160540" lon="10.1243930">
    <ele>23.3</ele>
    <time>2014-04-26T07:10:14Z</time>
   </trkpt>
   <trkpt lat="54.3160390" lon="10.1243480">
    <ele>23.5</ele>
    <time>2014-04-26T07:10:15Z</time>
   </trkpt>
   <trkpt lat="54.3160200" lon="10.1243070">
    <ele>23.7</ele>
    <time>2014-04-26T07:10:16Z</time>
   </trkpt>
   <trkpt lat="54.3160030" lon="10.1242620">
    <ele>24.0</ele>
    <time>2014-04-26T07:10:17Z</time>
   </trkpt>
   <trkpt lat="54.3159880" lon="10.1242160">
    <ele>24.2</ele>
    <time>2014-04-26T07:10:18Z</time>
   </trkpt>
   <trkpt lat="54.3159620" lon="10.1241880">
    <ele>24.4</ele>
    <time>2014-04-26T07:10:19Z</time>
   </trkpt>
   <trkpt lat="54.3159400" lon="10.1241540">
    <ele>24.7</ele>
    <time>2014-04-26T07:10:20Z</time>
   </trkpt>
   <trkpt lat="54.3159160" lon="10.1241240">
    <ele>24.9</ele>
    <time>2014-04-26T07:10:21Z</time>
   </trkpt>
   <trkpt lat="54.3158950" lon="10.1240900">
    <ele>25.1</ele>
    <time>2014-04-26T07:10:22Z</time>
   </trkpt>
   <trkpt lat="54.3158730" lon="10.1240590">
    <ele>25.4</ele>
    <time>2014-04-26T07:10:23Z</time>
   </trkpt>
   <trkpt lat="54.3158520" lon="10.1240280">
    <ele>25.6</ele>
    <time>2014-04-26T07:10:24Z</time>
   </trkpt>
   <trkpt lat="54.3158300" lon="10.1239930">
    <ele>25.8</ele>
    <time>2014-04-26T07:10:25Z</time>
   </trkpt>
   <trkpt lat="54.3157850" lon="10.1239350">
    <ele>26.3</ele>
    <time>2014-04-26T07:10:27Z</time>
   </trkpt>
   <trkpt lat="54.3157610" lon="10.1239040">
    <ele>26.6</ele>
    <time>2014-04-26T07:10:28Z</time>
   </trkpt>
   <trkpt lat="54.3157380" lon="10.1238710">
    <ele>26.8</ele>
    <time>2014-04-26T07:10:29Z</time>
   </trkpt>
   <trkpt lat="54.3157150" lon="10.1238360">
    <ele>27.1</ele>
    <time>2014-04-26T07:10:30Z</time>
   </trkpt>
   <trkpt lat="54.3156980" lon="10.1237940">
    <ele>27.4</ele>
    <time>2014-04-26T07:10:31Z</time>
   </trkpt>
   <trkpt lat="54.3156800" lon="10.1237540">
    <ele>27.7</ele>
    <time>2014-04-26T07:10:32Z</time>
   </trkpt>
   <trkpt lat="54.3156600" lon="10.1237140">
    <ele>27.9</ele>
    <time>2014-04-26T07:10:33Z</time>
   </trkpt>
   <trkpt lat="54.3156410" lon="10.1236720">
    <ele>28.1</ele>
    <time>2014-04-26T07:10:34Z</time>
   </trkpt>
   <trkpt lat="54.3156200" lon="10.1236310">
    <ele>28.3</ele>
    <time>2014-04-26T07:10:35Z</time>
   </trkpt>
   <trkpt lat="54.3155980" lon="10.1235920">
    <ele>28.5</ele>
    <time>2014-04-26T07:10:36Z</time>
   </trkpt>
   <trkpt lat="54.3155760" lon="10.1235500">
    <ele>28.7</ele>
    <time>2014-04-26T07:10:37Z</time>
   </trkpt>
   <trkpt lat="54.3155560" lon="10.1235060">
    <ele>28.9</ele>
    <time>2014-04-26T07:10:38Z</time>
   </trkpt>
   <trkpt lat="54.3155330" lon="10.1234650">
    <ele>29.1</ele>
    <time>2014-04-26T07:10:39Z</time>
   </trkpt>
   <trkpt lat="54.3154920" lon="10.1233820">
    <ele>29.5</ele>
    <time>2014-04-26T07:10:41Z</time>
   </trkpt>
   <trkpt lat="54.3154680" lon="10.1233370">
    <ele>29.7</ele>
    <time>2014-04-26T07:10:42Z</time>
   </trkpt>
   <trkpt lat="54.3154430" lon="10.1232970">
    <ele>29.9</ele>
    <time>2014-04-26T07:10:43Z</time>
   </trkpt>
   <trkpt lat="54.3154190" lon="10.1232570">
    <ele>30.2</ele>
    <time>2014-04-26T07:10:44Z</time>
   </trkpt>
   <trkpt lat="54.3153940" lon="10.1232200">
    <ele>30.3</ele>
    <time>2014-04-26T07:10:45Z</time>
   </trkpt>
   <trkpt lat="54.3153720" lon="10.1231780">
    <ele>30.5</ele>
    <time>2014-04-26T07:10:46Z</time>
   </trkpt>
   <trkpt lat="54.3153520" lon="10.1231340">
    <ele>30.6</ele>
    <time>2014-04-26T07:10:47Z</time>
   </trkpt>
   <trkpt lat="54.3153280" lon="10.1230970">
    <ele>30.8</ele>
    <time>2014-04-26T07:10:48Z</time>
   </trkpt>
   <trkpt lat="54.3153070" lon="10.1230530">
    <ele>30.9</ele>
    <time>2014-04-26T07:10:49Z</time>
   </trkpt>
   <trkpt lat="54.3152850" lon="10.1230130">
    <ele>31.1</ele>
    <time>2014-04-26T07:10:50Z</time>
   </trkpt>
   <trkpt lat="54.3152620" lon="10.1229760">
    <ele>31.2</ele>
    <time>2014-04-26T07:10:51Z</time>
   </trkpt>
   <trkpt lat="54.3152360" lon="10.1229440">
    <ele>31.4</ele>
    <time>2014-04-26T07:10:52Z</time>
   </trkpt>
   <trkpt lat="54.3152110" lon="10.1229110">
    <ele>31.5</ele>
    <time>2014-04-26T07:10:53Z</time>
   </trkpt>
   <trkpt lat="54.3151850" lon="10.1228820">
    <ele>31.6</ele>
    <time>2014-04-26T07:10:54Z</time>
   </trkpt>
   <trkpt lat="54.3151570" lon="10.1228540">
    <ele>31.7</ele>
    <time>2014-04-26T07:10:55Z</time>
   </trkpt>
   <trkpt lat="54.3151320" lon="10.1228230">
    <ele>31.8</ele>
    <time>2014-04-26T07:10:56Z</time>
   </trkpt>
   <trkpt lat="54.3151090" lon="10.1227910">
    <ele>31.9</ele>
    <time>2014-04-26T07:10:57Z</time>
   </trkpt>
   <trkpt lat="54.3150840" lon="10.1227590">
    <ele>32.1</ele>
    <time>2014-04-26T07:10:58Z</time>
   </trkpt>
   <trkpt lat="54.3150600" lon="10.1227240">
    <ele>32.2</ele>
    <time>2014-04-26T07:10:59Z</time>
   </trkpt>
   <trkpt lat="54.3150330" lon="10.1226930">
    <ele>32.3</ele>
    <time>2014-04-26T07:11:00Z</time>
   </trkpt>
   <trkpt lat="54.3150060" lon="10.1226590">
    <ele>32.4</ele>
    <time>2014-04-26T07:11:01Z</time>
   </trkpt>
   <trkpt lat="54.3149510" lon="10.1225970">
    <ele>32.7</ele>
    <time>2014-04-26T07:11:03Z</time>
   </trkpt>
   <trkpt lat="54.3149220" lon="10.1225640">
    <ele>32.9</ele>
    <time>2014-04-26T07:11:04Z</time>
   </trkpt>
   <trkpt lat="54.3148930" lon="10.1225320">
    <ele>33.0</ele>
    <time>2014-04-26T07:11:05Z</time>
   </trkpt>
   <trkpt lat="54.3148630" lon="10.1225030">
    <ele>33.2</ele>
    <time>2014-04-26T07:11:06Z</time>
   </trkpt>
   <trkpt lat="54.3148350" lon="10.1224730">
    <ele>33.3</ele>
    <time>2014-04-26T07:11:07Z</time>
   </trkpt>
   <trkpt lat="54.3147980" lon="10.1224470">
    <ele>33.5</ele>
    <time>2014-04-26T07:11:08Z</time>
   </trkpt>
   <trkpt lat="54.3147610" lon="10.1224250">
    <ele>33.6</ele>
    <time>2014-04-26T07:11:09Z</time>
   </trkpt>
   <trkpt lat="54.3147340" lon="10.1223920">
    <ele>33.8</ele>
    <time>2014-04-26T07:11:10Z</time>
   </trkpt>
   <trkpt lat="54.3146840" lon="10.1223830">
    <ele>34.0</ele>
    <time>2014-04-26T07:11:11Z</time>
   </trkpt>
   <trkpt lat="54.3146460" lon="10.1223600">
    <ele>34.1</ele>
    <time>2014-04-26T07:11:12Z</time>
   </trkpt>
   <trkpt lat="54.3146100" lon="10.1223360">
    <ele>34.3</ele>
    <time>2014-04-26T07:11:13Z</time>
   </trkpt>
   <trkpt lat="54.3145810" lon="10.1223070">
    <ele>34.5</ele>
    <time>2014-04-26T07:11:14Z</time>
   </trkpt>
   <trkpt lat="54.3145390" lon="10.1222890">
    <ele>34.5</ele>
    <time>2014-04-26T07:11:15Z</time>
   </trkpt>
   <trkpt lat="54.3145100" lon="10.1222610">
    <ele>34.6</ele>
    <time>2014-04-26T07:11:16Z</time>
   </trkpt>
   <trkpt lat="54.3144770" lon="10.1222390">
    <ele>34.6</ele>
    <time>2014-04-26T07:11:17Z</time>
   </trkpt>
   <trkpt lat="54.3144470" lon="10.1222140">
    <ele>34.7</ele>
    <time>2014-04-26T07:11:18Z</time>
   </trkpt>
   <trkpt lat="54.3144180" lon="10.1221860">
    <ele>34.7</ele>
    <time>2014-04-26T07:11:19Z</time>
   </trkpt>
   <trkpt lat="54.3143650" lon="10.1221420">
    <ele>34.8</ele>
    <time>2014-04-26T07:11:21Z</time>
   </trkpt>
   <trkpt lat="54.3143380" lon="10.1221150">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:22Z</time>
   </trkpt>
   <trkpt lat="54.3143140" lon="10.1220880">
    <ele>35.0</ele>
    <time>2014-04-26T07:11:23Z</time>
   </trkpt>
   <trkpt lat="54.3142910" lon="10.1220570">
    <ele>35.0</ele>
    <time>2014-04-26T07:11:24Z</time>
   </trkpt>
   <trkpt lat="54.3142660" lon="10.1220290">
    <ele>35.0</ele>
    <time>2014-04-26T07:11:25Z</time>
   </trkpt>
   <trkpt lat="54.3142370" lon="10.1220040">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:26Z</time>
   </trkpt>
   <trkpt lat="54.3142090" lon="10.1219770">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:27Z</time>
   </trkpt>
   <trkpt lat="54.3141790" lon="10.1219500">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:28Z</time>
   </trkpt>
   <trkpt lat="54.3141520" lon="10.1219180">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:29Z</time>
   </trkpt>
   <trkpt lat="54.3141030" lon="10.1218560">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:32Z</time>
   </trkpt>
   <trkpt lat="54.3140750" lon="10.1218280">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:33Z</time>
   </trkpt>
   <trkpt lat="54.3140220" lon="10.1217680">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:34Z</time>
   </trkpt>
   <trkpt lat="54.3139940" lon="10.1217400">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:35Z</time>
   </trkpt>
   <trkpt lat="54.3139670" lon="10.1217110">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:36Z</time>
   </trkpt>
   <trkpt lat="54.3139420" lon="10.1216760">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:38Z</time>
   </trkpt>
   <trkpt lat="54.3139130" lon="10.1216480">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:39Z</time>
   </trkpt>
   <trkpt lat="54.3138840" lon="10.1216160">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:40Z</time>
   </trkpt>
   <trkpt lat="54.3138570" lon="10.1215850">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:41Z</time>
   </trkpt>
   <trkpt lat="54.3138290" lon="10.1215550">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:42Z</time>
   </trkpt>
   <trkpt lat="54.3137730" lon="10.1214940">
    <ele>35.0</ele>
    <time>2014-04-26T07:11:43Z</time>
   </trkpt>
   <trkpt lat="54.3137460" lon="10.1214610">
    <ele>35.0</ele>
    <time>2014-04-26T07:11:45Z</time>
   </trkpt>
   <trkpt lat="54.3137150" lon="10.1214280">
    <ele>34.9</ele>
    <time>2014-04-26T07:11:46Z</time>
   </trkpt>
   <trkpt lat="54.3136750" lon="10.1214000">
    <ele>34.8</ele>
    <time>2014-04-26T07:11:47Z</time>
   </trkpt>
   <trkpt lat="54.3136400" lon="10.1213640">
    <ele>34.8</ele>
    <time>2014-04-26T07:11:48Z</time>
   </trkpt>
   <trkpt lat="54.3136040" lon="10.1213300">
    <ele>34.7</ele>
    <time>2014-04-26T07:11:49Z</time>
   </trkpt>
   <trkpt lat="54.3135320" lon="10.1212590">
    <ele>34.5</ele>
    <time>2014-04-26T07:11:50Z</time>
   </trkpt>
   <trkpt lat="54.3134720" lon="10.1212290">
    <ele>34.3</ele>
    <time>2014-04-26T07:11:51Z</time>
   </trkpt>
   <trkpt lat="54.3134410" lon="10.1211830">
    <ele>34.3</ele>
    <time>2014-04-26T07:11:52Z</time>
   </trkpt>
   <trkpt lat="54.3134100" lon="10.1211350">
    <ele>34.2</ele>
    <time>2014-04-26T07:11:53Z</time>
   </trkpt>
   <trkpt lat="54.3133740" lon="10.1210940">
    <ele>34.1</ele>
    <time>2014-04-26T07:11:54Z</time>
   </trkpt>
   <trkpt lat="54.3133460" lon="10.1210360">
    <ele>34.0</ele>
    <time>2014-04-26T07:11:55Z</time>
   </trkpt>
   <trkpt lat="54.3133160" lon="10.1209830">
    <ele>34.0</ele>
    <time>2014-04-26T07:11:56Z</time>
   </trkpt>
   <trkpt lat="54.3132860" lon="10.1209300">
    <ele>33.9</ele>
    <time>2014-04-26T07:11:57Z</time>
   </trkpt>
   <trkpt lat="54.3132540" lon="10.1208850">
    <ele>33.8</ele>
    <time>2014-04-26T07:11:58Z</time>
   </trkpt>
   <trkpt lat="54.3132300" lon="10.1208260">
    <ele>33.8</ele>
    <time>2014-04-26T07:11:59Z</time>
   </trkpt>
   <trkpt lat="54.3131930" lon="10.1207840">
    <ele>33.7</ele>
    <time>2014-04-26T07:12:01Z</time>
   </trkpt>
   <trkpt lat="54.3131570" lon="10.1207490">
    <ele>33.6</ele>
    <time>2014-04-26T07:12:02Z</time>
   </trkpt>
   <trkpt lat="54.3130940" lon="10.1206670">
    <ele>33.4</ele>
    <time>2014-04-26T07:12:04Z</time>
   </trkpt>
   <trkpt lat="54.3130660" lon="10.1206240">
    <ele>33.4</ele>
    <time>2014-04-26T07:12:05Z</time>
   </trkpt>
   <trkpt lat="54.3130410" lon="10.1205850">
    <ele>33.3</ele>
    <time>2014-04-26T07:12:06Z</time>
   </trkpt>
   <trkpt lat="54.3130170" lon="10.1205480">
    <ele>33.2</ele>
    <time>2014-04-26T07:12:07Z</time>
   </trkpt>
   <trkpt lat="54.3129920" lon="10.1205130">
    <ele>33.2</ele>
    <time>2014-04-26T07:12:08Z</time>
   </trkpt>
   <trkpt lat="54.3129700" lon="10.1204780">
    <ele>33.1</ele>
    <time>2014-04-26T07:12:09Z</time>
   </trkpt>
   <trkpt lat="54.3129430" lon="10.1204420">
    <ele>33.1</ele>
    <time>2014-04-26T07:12:10Z</time>
   </trkpt>
   <trkpt lat="54.3128770" lon="10.1203530">
    <ele>32.9</ele>
    <time>2014-04-26T07:12:13Z</time>
   </trkpt>
   <trkpt lat="54.3128550" lon="10.1203240">
    <ele>32.8</ele>
    <time>2014-04-26T07:12:14Z</time>
   </trkpt>
   <trkpt lat="54.3128130" lon="10.1202660">
    <ele>32.7</ele>
    <time>2014-04-26T07:12:16Z</time>
   </trkpt>
   <trkpt lat="54.3127810" lon="10.1201940">
    <ele>32.7</ele>
    <time>2014-04-26T07:12:17Z</time>
   </trkpt>
   <trkpt lat="54.3127660" lon="10.1201500">
    <ele>32.7</ele>
    <time>2014-04-26T07:12:19Z</time>
   </trkpt>
   <trkpt lat="54.3127520" lon="10.1201040">
    <ele>32.7</ele>
    <time>2014-04-26T07:12:20Z</time>
   </trkpt>
   <trkpt lat="54.3127370" lon="10.1200630">
    <ele>32.7</ele>
    <time>2014-04-26T07:12:21Z</time>
   </trkpt>
   <trkpt lat="54.3126980" lon="10.1199910">
    <ele>32.6</ele>
    <time>2014-04-26T07:12:22Z</time>
   </trkpt>
   <trkpt lat="54.3126690" lon="10.1199140">
    <ele>32.5</ele>
    <time>2014-04-26T07:12:24Z</time>
   </trkpt>
   <trkpt lat="54.3126560" lon="10.1198590">
    <ele>32.6</ele>
    <time>2014-04-26T07:12:25Z</time>
   </trkpt>
   <trkpt lat="54.3126460" lon="10.1198000">
    <ele>32.6</ele>
    <time>2014-04-26T07:12:27Z</time>
   </trkpt>
   <trkpt lat="54.3126410" lon="10.1197340">
    <ele>32.7</ele>
    <time>2014-04-26T07:12:28Z</time>
   </trkpt>
   <trkpt lat="54.3126350" lon="10.1196630">
    <ele>32.8</ele>
    <time>2014-04-26T07:12:29Z</time>
   </trkpt>
   <trkpt lat="54.3126260" lon="10.1195820">
    <ele>32.9</ele>
    <time>2014-04-26T07:12:30Z</time>
   </trkpt>
   <trkpt lat="54.3126010" lon="10.1194300">
    <ele>32.9</ele>
    <time>2014-04-26T07:12:31Z</time>
   </trkpt>
   <trkpt lat="54.3125780" lon="10.1193600">
    <ele>32.8</ele>
    <time>2014-04-26T07:12:32Z</time>
   </trkpt>
   <trkpt lat="54.3125200" lon="10.1192400">
    <ele>32.6</ele>
    <time>2014-04-26T07:12:34Z</time>
   </trkpt>
   <trkpt lat="54.3124800" lon="10.1192170">
    <ele>32.3</ele>
    <time>2014-04-26T07:12:35Z</time>
   </trkpt>
   <trkpt lat="54.3124790" lon="10.1192860">
    <ele>32.2</ele>
    <time>2014-04-26T07:12:36Z</time>
   </trkpt>
   <trkpt lat="54.3124770" lon="10.1193970">
    <ele>32.1</ele>
    <time>2014-04-26T07:12:37Z</time>
   </trkpt>
   <trkpt lat="54.3124970" lon="10.1194650">
    <ele>32.1</ele>
    <time>2014-04-26T07:12:39Z</time>
   </trkpt>
   <trkpt lat="54.3125110" lon="10.1195280">
    <ele>32.1</ele>
    <time>2014-04-26T07:12:40Z</time>
   </trkpt>
   <trkpt lat="54.3125400" lon="10.1195600">
    <ele>32.3</ele>
    <time>2014-04-26T07:12:41Z</time>
   </trkpt>
   <trkpt lat="54.3125720" lon="10.1195900">
    <ele>32.5</ele>
    <time>2014-04-26T07:12:42Z</time>
   </trkpt>
   <trkpt lat="54.3126110" lon="10.1195570">
    <ele>32.8</ele>
    <time>2014-04-26T07:12:43Z</time>
   </trkpt>
   <trkpt lat="54.3126210" lon="10.1195030">
    <ele>33.0</ele>
    <time>2014-04-26T07:12:44Z</time>
   </trkpt>
   <trkpt lat="54.3125810" lon="10.1194170">
    <ele>32.8</ele>
    <time>2014-04-26T07:12:46Z</time>
   </trkpt>
   <trkpt lat="54.3125600" lon="10.1193770">
    <ele>32.7</ele>
    <time>2014-04-26T07:12:47Z</time>
   </trkpt>
   <trkpt lat="54.3125010" lon="10.1192840">
    <ele>32.4</ele>
    <time>2014-04-26T07:12:49Z</time>
   </trkpt>
   <trkpt lat="54.3124730" lon="10.1192440">
    <ele>32.2</ele>
    <time>2014-04-26T07:12:50Z</time>
   </trkpt>
   <trkpt lat="54.3124240" lon="10.1191530">
    <ele>32.0</ele>
    <time>2014-04-26T07:12:52Z</time>
   </trkpt>
   <trkpt lat="54.3124010" lon="10.1191030">
    <ele>31.9</ele>
    <time>2014-04-26T07:12:53Z</time>
   </trkpt>
   <trkpt lat="54.3123770" lon="10.1190530">
    <ele>31.8</ele>
    <time>2014-04-26T07:12:54Z</time>
   </trkpt>
   <trkpt lat="54.3123520" lon="10.1190000">
    <ele>31.6</ele>
    <time>2014-04-26T07:12:55Z</time>
   </trkpt>
   <trkpt lat="54.3123100" lon="10.1189090">
    <ele>31.4</ele>
    <time>2014-04-26T07:12:56Z</time>
   </trkpt>
   <trkpt lat="54.3122700" lon="10.1188550">
    <ele>31.2</ele>
    <time>2014-04-26T07:12:57Z</time>
   </trkpt>
   <trkpt lat="54.3121870" lon="10.1187560">
    <ele>30.7</ele>
    <time>2014-04-26T07:12:59Z</time>
   </trkpt>
   <trkpt lat="54.3121660" lon="10.1187050">
    <ele>30.8</ele>
    <time>2014-04-26T07:13:00Z</time>
   </trkpt>
   <trkpt lat="54.3121430" lon="10.1186540">
    <ele>30.9</ele>
    <time>2014-04-26T07:13:01Z</time>
   </trkpt>
   <trkpt lat="54.3121020" lon="10.1185920">
    <ele>30.9</ele>
    <time>2014-04-26T07:13:02Z</time>
   </trkpt>
   <trkpt lat="54.3120630" lon="10.1185360">
    <ele>30.9</ele>
    <time>2014-04-26T07:13:03Z</time>
   </trkpt>
   <trkpt lat="54.3120310" lon="10.1184780">
    <ele>31.0</ele>
    <time>2014-04-26T07:13:04Z</time>
   </trkpt>
   <trkpt lat="54.3119640" lon="10.1183530">
    <ele>31.2</ele>
    <time>2014-04-26T07:13:06Z</time>
   </trkpt>
   <trkpt lat="54.3119330" lon="10.1182880">
    <ele>31.3</ele>
    <time>2014-04-26T07:13:07Z</time>
   </trkpt>
   <trkpt lat="54.3118900" lon="10.1182310">
    <ele>31.3</ele>
    <time>2014-04-26T07:13:08Z</time>
   </trkpt>
   <trkpt lat="54.3118560" lon="10.1181710">
    <ele>31.4</ele>
    <time>2014-04-26T07:13:09Z</time>
   </trkpt>
   <trkpt lat="54.3118270" lon="10.1181080">
    <ele>31.5</ele>
    <time>2014-04-26T07:13:10Z</time>
   </trkpt>
   <trkpt lat="54.3118000" lon="10.1180360">
    <ele>31.7</ele>
    <time>2014-04-26T07:13:11Z</time>
   </trkpt>
   <trkpt lat="54.3117460" lon="10.1179830">
    <ele>31.7</ele>
    <time>2014-04-26T07:13:12Z</time>
   </trkpt>
   <trkpt lat="54.3116920" lon="10.1179400">
    <ele>31.5</ele>
    <time>2014-04-26T07:13:13Z</time>
   </trkpt>
   <trkpt lat="54.3116500" lon="10.1178750">
    <ele>31.6</ele>
    <time>2014-04-26T07:13:14Z</time>
   </trkpt>
   <trkpt lat="54.3116160" lon="10.1178170">
    <ele>31.7</ele>
    <time>2014-04-26T07:13:15Z</time>
   </trkpt>
   <trkpt lat="54.3115810" lon="10.1177640">
    <ele>31.7</ele>
    <time>2014-04-26T07:13:16Z</time>
   </trkpt>
   <trkpt lat="54.3115320" lon="10.1177120">
    <ele>31.7</ele>
    <time>2014-04-26T07:13:17Z</time>
   </trkpt>
   <trkpt lat="54.3114950" lon="10.1176480">
    <ele>31.8</ele>
    <time>2014-04-26T07:13:18Z</time>
   </trkpt>
   <trkpt lat="54.3114610" lon="10.1175920">
    <ele>31.8</ele>
    <time>2014-04-26T07:13:19Z</time>
   </trkpt>
   <trkpt lat="54.3114360" lon="10.1175190">
    <ele>32.0</ele>
    <time>2014-04-26T07:13:20Z</time>
   </trkpt>
   <trkpt lat="54.3113750" lon="10.1173930">
    <ele>32.3</ele>
    <time>2014-04-26T07:13:22Z</time>
   </trkpt>
   <trkpt lat="54.3113570" lon="10.1173350">
    <ele>32.4</ele>
    <time>2014-04-26T07:13:23Z</time>
   </trkpt>
   <trkpt lat="54.3113320" lon="10.1173010">
    <ele>32.4</ele>
    <time>2014-04-26T07:13:24Z</time>
   </trkpt>
   <trkpt lat="54.3113000" lon="10.1172380">
    <ele>32.6</ele>
    <time>2014-04-26T07:13:25Z</time>
   </trkpt>
   <trkpt lat="54.3112700" lon="10.1171760">
    <ele>32.7</ele>
    <time>2014-04-26T07:13:26Z</time>
   </trkpt>
   <trkpt lat="54.3112420" lon="10.1171210">
    <ele>32.8</ele>
    <time>2014-04-26T07:13:27Z</time>
   </trkpt>
   <trkpt lat="54.3112130" lon="10.1170630">
    <ele>33.0</ele>
    <time>2014-04-26T07:13:28Z</time>
   </trkpt>
   <trkpt lat="54.3111820" lon="10.1170150">
    <ele>33.1</ele>
    <time>2014-04-26T07:13:29Z</time>
   </trkpt>
   <trkpt lat="54.3111510" lon="10.1169660">
    <ele>33.2</ele>
    <time>2014-04-26T07:13:30Z</time>
   </trkpt>
   <trkpt lat="54.3111220" lon="10.1169280">
    <ele>33.2</ele>
    <time>2014-04-26T07:13:31Z</time>
   </trkpt>
   <trkpt lat="54.3110940" lon="10.1168860">
    <ele>33.3</ele>
    <time>2014-04-26T07:13:32Z</time>
   </trkpt>
   <trkpt lat="54.3110690" lon="10.1168470">
    <ele>33.3</ele>
    <time>2014-04-26T07:13:33Z</time>
   </trkpt>
   <trkpt lat="54.3110420" lon="10.1168120">
    <ele>33.4</ele>
    <time>2014-04-26T07:13:34Z</time>
   </trkpt>
   <trkpt lat="54.3110210" lon="10.1167750">
    <ele>33.5</ele>
    <time>2014-04-26T07:13:35Z</time>
   </trkpt>
   <trkpt lat="54.3109900" lon="10.1167150">
    <ele>33.6</ele>
    <time>2014-04-26T07:13:37Z</time>
   </trkpt>
   <trkpt lat="54.3109670" lon="10.1166800">
    <ele>33.6</ele>
    <time>2014-04-26T07:13:40Z</time>
   </trkpt>
   <trkpt lat="54.3109430" lon="10.1166520">
    <ele>33.7</ele>
    <time>2014-04-26T07:13:42Z</time>
   </trkpt>
   <trkpt lat="54.3109080" lon="10.1165890">
    <ele>33.8</ele>
    <time>2014-04-26T07:13:44Z</time>
   </trkpt>
   <trkpt lat="54.3108820" lon="10.1165390">
    <ele>33.9</ele>
    <time>2014-04-26T07:13:45Z</time>
   </trkpt>
   <trkpt lat="54.3108540" lon="10.1164830">
    <ele>34.0</ele>
    <time>2014-04-26T07:13:46Z</time>
   </trkpt>
   <trkpt lat="54.3108220" lon="10.1164300">
    <ele>34.1</ele>
    <time>2014-04-26T07:13:47Z</time>
   </trkpt>
   <trkpt lat="54.3107920" lon="10.1163740">
    <ele>34.2</ele>
    <time>2014-04-26T07:13:48Z</time>
   </trkpt>
   <trkpt lat="54.3107610" lon="10.1163170">
    <ele>34.3</ele>
    <time>2014-04-26T07:13:49Z</time>
   </trkpt>
   <trkpt lat="54.3107310" lon="10.1162640">
    <ele>34.3</ele>
    <time>2014-04-26T07:13:50Z</time>
   </trkpt>
   <trkpt lat="54.3106730" lon="10.1161590">
    <ele>34.5</ele>
    <time>2014-04-26T07:13:52Z</time>
   </trkpt>
   <trkpt lat="54.3106430" lon="10.1161040">
    <ele>34.6</ele>
    <time>2014-04-26T07:13:53Z</time>
   </trkpt>
   <trkpt lat="54.3106040" lon="10.1160270">
    <ele>34.7</ele>
    <time>2014-04-26T07:13:54Z</time>
   </trkpt>
   <trkpt lat="54.3105720" lon="10.1159800">
    <ele>34.8</ele>
    <time>2014-04-26T07:13:55Z</time>
   </trkpt>
   <trkpt lat="54.3105410" lon="10.1159260">
    <ele>34.9</ele>
    <time>2014-04-26T07:13:56Z</time>
   </trkpt>
   <trkpt lat="54.3104720" lon="10.1158000">
    <ele>35.1</ele>
    <time>2014-04-26T07:13:58Z</time>
   </trkpt>
   <trkpt lat="54.3104380" lon="10.1157450">
    <ele>35.2</ele>
    <time>2014-04-26T07:13:59Z</time>
   </trkpt>
   <trkpt lat="54.3104040" lon="10.1156860">
    <ele>35.3</ele>
    <time>2014-04-26T07:14:00Z</time>
   </trkpt>
   <trkpt lat="54.3103750" lon="10.1156360">
    <ele>35.4</ele>
    <time>2014-04-26T07:14:01Z</time>
   </trkpt>
   <trkpt lat="54.3103290" lon="10.1155750">
    <ele>35.5</ele>
    <time>2014-04-26T07:14:02Z</time>
   </trkpt>
   <trkpt lat="54.3103110" lon="10.1154900">
    <ele>35.7</ele>
    <time>2014-04-26T07:14:03Z</time>
   </trkpt>
   <trkpt lat="54.3102820" lon="10.1154170">
    <ele>35.8</ele>
    <time>2014-04-26T07:14:04Z</time>
   </trkpt>
   <trkpt lat="54.3102100" lon="10.1153150">
    <ele>35.6</ele>
    <time>2014-04-26T07:14:05Z</time>
   </trkpt>
   <trkpt lat="54.3101590" lon="10.1152680">
    <ele>35.5</ele>
    <time>2014-04-26T07:14:06Z</time>
   </trkpt>
   <trkpt lat="54.3101160" lon="10.1152360">
    <ele>35.4</ele>
    <time>2014-04-26T07:14:08Z</time>
   </trkpt>
   <trkpt lat="54.3100750" lon="10.1151940">
    <ele>35.3</ele>
    <time>2014-04-26T07:14:09Z</time>
   </trkpt>
   <trkpt lat="54.3100480" lon="10.1151130">
    <ele>35.2</ele>
    <time>2014-04-26T07:14:10Z</time>
   </trkpt>
   <trkpt lat="54.3100100" lon="10.1150500">
    <ele>35.1</ele>
    <time>2014-04-26T07:14:11Z</time>
   </trkpt>
   <trkpt lat="54.3099910" lon="10.1149720">
    <ele>35.0</ele>
    <time>2014-04-26T07:14:12Z</time>
   </trkpt>
   <trkpt lat="54.3099200" lon="10.1148630">
    <ele>34.7</ele>
    <time>2014-04-26T07:14:14Z</time>
   </trkpt>
   <trkpt lat="54.3098850" lon="10.1147890">
    <ele>34.6</ele>
    <time>2014-04-26T07:14:15Z</time>
   </trkpt>
   <trkpt lat="54.3098250" lon="10.1146590">
    <ele>34.4</ele>
    <time>2014-04-26T07:14:16Z</time>
   </trkpt>
   <trkpt lat="54.3097940" lon="10.1146020">
    <ele>34.3</ele>
    <time>2014-04-26T07:14:18Z</time>
   </trkpt>
   <trkpt lat="54.3097740" lon="10.1145340">
    <ele>34.2</ele>
    <time>2014-04-26T07:14:19Z</time>
   </trkpt>
   <trkpt lat="54.3097450" lon="10.1144700">
    <ele>34.2</ele>
    <time>2014-04-26T07:14:20Z</time>
   </trkpt>
   <trkpt lat="54.3097130" lon="10.1143960">
    <ele>34.1</ele>
    <time>2014-04-26T07:14:21Z</time>
   </trkpt>
   <trkpt lat="54.3096760" lon="10.1143350">
    <ele>34.1</ele>
    <time>2014-04-26T07:14:22Z</time>
   </trkpt>
   <trkpt lat="54.3096400" lon="10.1142820">
    <ele>34.0</ele>
    <time>2014-04-26T07:14:23Z</time>
   </trkpt>
   <trkpt lat="54.3096060" lon="10.1142150">
    <ele>34.0</ele>
    <time>2014-04-26T07:14:24Z</time>
   </trkpt>
   <trkpt lat="54.3095720" lon="10.1141520">
    <ele>33.9</ele>
    <time>2014-04-26T07:14:25Z</time>
   </trkpt>
   <trkpt lat="54.3095230" lon="10.1140960">
    <ele>33.5</ele>
    <time>2014-04-26T07:14:26Z</time>
   </trkpt>
   <trkpt lat="54.3094510" lon="10.1139970">
    <ele>32.9</ele>
    <time>2014-04-26T07:14:28Z</time>
   </trkpt>
   <trkpt lat="54.3094310" lon="10.1139300">
    <ele>32.7</ele>
    <time>2014-04-26T07:14:29Z</time>
   </trkpt>
   <trkpt lat="54.3093990" lon="10.1138680">
    <ele>32.5</ele>
    <time>2014-04-26T07:14:30Z</time>
   </trkpt>
   <trkpt lat="54.3093280" lon="10.1137530">
    <ele>31.9</ele>
    <time>2014-04-26T07:14:32Z</time>
   </trkpt>
   <trkpt lat="54.3092950" lon="10.1136810">
    <ele>31.5</ele>
    <time>2014-04-26T07:14:33Z</time>
   </trkpt>
   <trkpt lat="54.3092290" lon="10.1135720">
    <ele>30.9</ele>
    <time>2014-04-26T07:14:34Z</time>
   </trkpt>
   <trkpt lat="54.3091950" lon="10.1135120">
    <ele>30.6</ele>
    <time>2014-04-26T07:14:35Z</time>
   </trkpt>
   <trkpt lat="54.3091520" lon="10.1134590">
    <ele>30.2</ele>
    <time>2014-04-26T07:14:36Z</time>
   </trkpt>
   <trkpt lat="54.3091260" lon="10.1133960">
    <ele>30.0</ele>
    <time>2014-04-26T07:14:38Z</time>
   </trkpt>
   <trkpt lat="54.3091040" lon="10.1133370">
    <ele>29.8</ele>
    <time>2014-04-26T07:14:39Z</time>
   </trkpt>
   <trkpt lat="54.3090780" lon="10.1132910">
    <ele>29.7</ele>
    <time>2014-04-26T07:14:40Z</time>
   </trkpt>
   <trkpt lat="54.3090540" lon="10.1132530">
    <ele>29.5</ele>
    <time>2014-04-26T07:14:41Z</time>
   </trkpt>
   <trkpt lat="54.3090120" lon="10.1131990">
    <ele>29.2</ele>
    <time>2014-04-26T07:14:43Z</time>
   </trkpt>
   <trkpt lat="54.3090010" lon="10.1131120">
    <ele>29.2</ele>
    <time>2014-04-26T07:14:45Z</time>
   </trkpt>
   <trkpt lat="54.3090080" lon="10.1130760">
    <ele>29.2</ele>
    <time>2014-04-26T07:15:13Z</time>
   </trkpt>
   <trkpt lat="54.3090140" lon="10.1130410">
    <ele>29.3</ele>
    <time>2014-04-26T07:15:35Z</time>
   </trkpt>
   <trkpt lat="54.3090210" lon="10.1130060">
    <ele>29.3</ele>
    <time>2014-04-26T07:15:37Z</time>
   </trkpt>
   <trkpt lat="54.3090330" lon="10.1129390">
    <ele>29.4</ele>
    <time>2014-04-26T07:15:38Z</time>
   </trkpt>
   <trkpt lat="54.3090600" lon="10.1129030">
    <ele>29.5</ele>
    <time>2014-04-26T07:15:40Z</time>
   </trkpt>
   <trkpt lat="54.3090860" lon="10.1128660">
    <ele>29.6</ele>
    <time>2014-04-26T07:15:41Z</time>
   </trkpt>
   <trkpt lat="54.3091120" lon="10.1128290">
    <ele>29.7</ele>
    <time>2014-04-26T07:15:42Z</time>
   </trkpt>
   <trkpt lat="54.3091370" lon="10.1127910">
    <ele>29.8</ele>
    <time>2014-04-26T07:15:43Z</time>
   </trkpt>
   <trkpt lat="54.3091640" lon="10.1127570">
    <ele>29.9</ele>
    <time>2014-04-26T07:15:44Z</time>
   </trkpt>
   <trkpt lat="54.3091910" lon="10.1127220">
    <ele>30.0</ele>
    <time>2014-04-26T07:15:45Z</time>
   </trkpt>
   <trkpt lat="54.3092470" lon="10.1126420">
    <ele>30.2</ele>
    <time>2014-04-26T07:15:47Z</time>
   </trkpt>
   <trkpt lat="54.3092740" lon="10.1125960">
    <ele>30.3</ele>
    <time>2014-04-26T07:15:48Z</time>
   </trkpt>
   <trkpt lat="54.3092990" lon="10.1125520">
    <ele>30.3</ele>
    <time>2014-04-26T07:15:49Z</time>
   </trkpt>
   <trkpt lat="54.3093280" lon="10.1125120">
    <ele>30.4</ele>
    <time>2014-04-26T07:15:50Z</time>
   </trkpt>
   <trkpt lat="54.3093890" lon="10.1124320">
    <ele>30.6</ele>
    <time>2014-04-26T07:15:52Z</time>
   </trkpt>
   <trkpt lat="54.3094170" lon="10.1123900">
    <ele>30.7</ele>
    <time>2014-04-26T07:15:53Z</time>
   </trkpt>
   <trkpt lat="54.3094690" lon="10.1122960">
    <ele>30.8</ele>
    <time>2014-04-26T07:15:55Z</time>
   </trkpt>
   <trkpt lat="54.3094950" lon="10.1122430">
    <ele>30.9</ele>
    <time>2014-04-26T07:15:56Z</time>
   </trkpt>
   <trkpt lat="54.3095260" lon="10.1121860">
    <ele>30.9</ele>
    <time>2014-04-26T07:15:57Z</time>
   </trkpt>
   <trkpt lat="54.3095590" lon="10.1121290">
    <ele>31.0</ele>
    <time>2014-04-26T07:15:58Z</time>
   </trkpt>
   <trkpt lat="54.3095920" lon="10.1120710">
    <ele>31.0</ele>
    <time>2014-04-26T07:15:59Z</time>
   </trkpt>
   <trkpt lat="54.3096280" lon="10.1120180">
    <ele>31.2</ele>
    <time>2014-04-26T07:16:00Z</time>
   </trkpt>
   <trkpt lat="54.3096640" lon="10.1119690">
    <ele>31.3</ele>
    <time>2014-04-26T07:16:01Z</time>
   </trkpt>
   <trkpt lat="54.3097380" lon="10.1118860">
    <ele>31.5</ele>
    <time>2014-04-26T07:16:03Z</time>
   </trkpt>
   <trkpt lat="54.3097780" lon="10.1118520">
    <ele>31.6</ele>
    <time>2014-04-26T07:16:04Z</time>
   </trkpt>
   <trkpt lat="54.3098170" lon="10.1118190">
    <ele>31.7</ele>
    <time>2014-04-26T07:16:05Z</time>
   </trkpt>
   <trkpt lat="54.3098570" lon="10.1117880">
    <ele>31.8</ele>
    <time>2014-04-26T07:16:06Z</time>
   </trkpt>
   <trkpt lat="54.3099370" lon="10.1117260">
    <ele>31.9</ele>
    <time>2014-04-26T07:16:07Z</time>
   </trkpt>
   <trkpt lat="54.3099780" lon="10.1116920">
    <ele>32.0</ele>
    <time>2014-04-26T07:16:09Z</time>
   </trkpt>
   <trkpt lat="54.3100410" lon="10.1116600">
    <ele>32.1</ele>
    <time>2014-04-26T07:16:10Z</time>
   </trkpt>
   <trkpt lat="54.3100960" lon="10.1116450">
    <ele>32.2</ele>
    <time>2014-04-26T07:16:11Z</time>
   </trkpt>
   <trkpt lat="54.3101780" lon="10.1115780">
    <ele>32.3</ele>
    <time>2014-04-26T07:16:12Z</time>
   </trkpt>
   <trkpt lat="54.3102260" lon="10.1115450">
    <ele>32.3</ele>
    <time>2014-04-26T07:16:14Z</time>
   </trkpt>
   <trkpt lat="54.3103130" lon="10.1114710">
    <ele>32.3</ele>
    <time>2014-04-26T07:16:15Z</time>
   </trkpt>
   <trkpt lat="54.3103680" lon="10.1114500">
    <ele>32.4</ele>
    <time>2014-04-26T07:16:16Z</time>
   </trkpt>
   <trkpt lat="54.3104080" lon="10.1114210">
    <ele>32.4</ele>
    <time>2014-04-26T07:16:17Z</time>
   </trkpt>
   <trkpt lat="54.3104360" lon="10.1113720">
    <ele>32.4</ele>
    <time>2014-04-26T07:16:19Z</time>
   </trkpt>
   <trkpt lat="54.3105220" lon="10.1113220">
    <ele>32.5</ele>
    <time>2014-04-26T07:16:20Z</time>
   </trkpt>
   <trkpt lat="54.3105550" lon="10.1113100">
    <ele>32.6</ele>
    <time>2014-04-26T07:16:21Z</time>
   </trkpt>
   <trkpt lat="54.3105960" lon="10.1112920">
    <ele>32.7</ele>
    <time>2014-04-26T07:16:22Z</time>
   </trkpt>
   <trkpt lat="54.3106470" lon="10.1112450">
    <ele>32.8</ele>
    <time>2014-04-26T07:16:23Z</time>
   </trkpt>
   <trkpt lat="54.3106860" lon="10.1112230">
    <ele>32.9</ele>
    <time>2014-04-26T07:16:24Z</time>
   </trkpt>
   <trkpt lat="54.3107270" lon="10.1112020">
    <ele>33.1</ele>
    <time>2014-04-26T07:16:25Z</time>
   </trkpt>
   <trkpt lat="54.3107750" lon="10.1111650">
    <ele>33.2</ele>
    <time>2014-04-26T07:16:26Z</time>
   </trkpt>
   <trkpt lat="54.3108210" lon="10.1111400">
    <ele>33.4</ele>
    <time>2014-04-26T07:16:27Z</time>
   </trkpt>
   <trkpt lat="54.3108650" lon="10.1111210">
    <ele>33.5</ele>
    <time>2014-04-26T07:16:28Z</time>
   </trkpt>
   <trkpt lat="54.3109040" lon="10.1110840">
    <ele>33.7</ele>
    <time>2014-04-26T07:16:29Z</time>
   </trkpt>
   <trkpt lat="54.3109370" lon="10.1110680">
    <ele>33.8</ele>
    <time>2014-04-26T07:16:30Z</time>
   </trkpt>
   <trkpt lat="54.3109750" lon="10.1110480">
    <ele>33.9</ele>
    <time>2014-04-26T07:16:31Z</time>
   </trkpt>
   <trkpt lat="54.3110220" lon="10.1110190">
    <ele>34.1</ele>
    <time>2014-04-26T07:16:33Z</time>
   </trkpt>
   <trkpt lat="54.3111080" lon="10.1109640">
    <ele>34.4</ele>
    <time>2014-04-26T07:16:34Z</time>
   </trkpt>
   <trkpt lat="54.3111620" lon="10.1109580">
    <ele>34.6</ele>
    <time>2014-04-26T07:16:35Z</time>
   </trkpt>
   <trkpt lat="54.3111980" lon="10.1109440">
    <ele>34.8</ele>
    <time>2014-04-26T07:16:37Z</time>
   </trkpt>
   <trkpt lat="54.3112410" lon="10.1109290">
    <ele>35.0</ele>
    <time>2014-04-26T07:16:38Z</time>
   </trkpt>
   <trkpt lat="54.3112800" lon="10.1109130">
    <ele>35.0</ele>
    <time>2014-04-26T07:16:39Z</time>
   </trkpt>
   <trkpt lat="54.3113070" lon="10.1109110">
    <ele>35.0</ele>
    <time>2014-04-26T07:16:40Z</time>
   </trkpt>
   <trkpt lat="54.3113420" lon="10.1109020">
    <ele>35.1</ele>
    <time>2014-04-26T07:16:41Z</time>
   </trkpt>
   <trkpt lat="54.3113810" lon="10.1108640">
    <ele>35.1</ele>
    <time>2014-04-26T07:16:42Z</time>
   </trkpt>
   <trkpt lat="54.3114950" lon="10.1108230">
    <ele>35.1</ele>
    <time>2014-04-26T07:16:44Z</time>
   </trkpt>
   <trkpt lat="54.3115880" lon="10.1107800">
    <ele>35.2</ele>
    <time>2014-04-26T07:16:46Z</time>
   </trkpt>
   <trkpt lat="54.3116390" lon="10.1107500">
    <ele>35.2</ele>
    <time>2014-04-26T07:16:47Z</time>
   </trkpt>
   <trkpt lat="54.3116890" lon="10.1107250">
    <ele>35.2</ele>
    <time>2014-04-26T07:16:48Z</time>
   </trkpt>
   <trkpt lat="54.3117770" lon="10.1106930">
    <ele>35.2</ele>
    <time>2014-04-26T07:16:49Z</time>
   </trkpt>
   <trkpt lat="54.3118100" lon="10.1106750">
    <ele>35.2</ele>
    <time>2014-04-26T07:16:51Z</time>
   </trkpt>
   <trkpt lat="54.3118390" lon="10.1106570">
    <ele>35.2</ele>
    <time>2014-04-26T07:16:52Z</time>
   </trkpt>
   <trkpt lat="54.3118810" lon="10.1106150">
    <ele>35.2</ele>
    <time>2014-04-26T07:16:54Z</time>
   </trkpt>
   <trkpt lat="54.3119330" lon="10.1105510">
    <ele>35.1</ele>
    <time>2014-04-26T07:16:58Z</time>
   </trkpt>
   <trkpt lat="54.3119480" lon="10.1105100">
    <ele>35.1</ele>
    <time>2014-04-26T07:16:59Z</time>
   </trkpt>
   <trkpt lat="54.3119600" lon="10.1104560">
    <ele>35.0</ele>
    <time>2014-04-26T07:17:00Z</time>
   </trkpt>
   <trkpt lat="54.3119640" lon="10.1103950">
    <ele>34.9</ele>
    <time>2014-04-26T07:17:01Z</time>
   </trkpt>
   <trkpt lat="54.3119560" lon="10.1103330">
    <ele>34.7</ele>
    <time>2014-04-26T07:17:02Z</time>
   </trkpt>
   <trkpt lat="54.3119440" lon="10.1102730">
    <ele>34.5</ele>
    <time>2014-04-26T07:17:03Z</time>
   </trkpt>
   <trkpt lat="54.3119320" lon="10.1102100">
    <ele>34.3</ele>
    <time>2014-04-26T07:17:04Z</time>
   </trkpt>
   <trkpt lat="54.3119250" lon="10.1101430">
    <ele>34.1</ele>
    <time>2014-04-26T07:17:05Z</time>
   </trkpt>
   <trkpt lat="54.3119150" lon="10.1100580">
    <ele>33.8</ele>
    <time>2014-04-26T07:17:06Z</time>
   </trkpt>
   <trkpt lat="54.3118790" lon="10.1099060">
    <ele>33.3</ele>
    <time>2014-04-26T07:17:08Z</time>
   </trkpt>
   <trkpt lat="54.3118590" lon="10.1098350">
    <ele>33.1</ele>
    <time>2014-04-26T07:17:09Z</time>
   </trkpt>
   <trkpt lat="54.3118430" lon="10.1097770">
    <ele>32.9</ele>
    <time>2014-04-26T07:17:10Z</time>
   </trkpt>
   <trkpt lat="54.3118220" lon="10.1096990">
    <ele>32.7</ele>
    <time>2014-04-26T07:17:11Z</time>
   </trkpt>
   <trkpt lat="54.3118010" lon="10.1096440">
    <ele>32.5</ele>
    <time>2014-04-26T07:17:12Z</time>
   </trkpt>
   <trkpt lat="54.3117870" lon="10.1095790">
    <ele>32.3</ele>
    <time>2014-04-26T07:17:13Z</time>
   </trkpt>
   <trkpt lat="54.3117720" lon="10.1095080">
    <ele>32.2</ele>
    <time>2014-04-26T07:17:14Z</time>
   </trkpt>
   <trkpt lat="54.3117590" lon="10.1094640">
    <ele>32.1</ele>
    <time>2014-04-26T07:17:15Z</time>
   </trkpt>
   <trkpt lat="54.3117590" lon="10.1094020">
    <ele>32.0</ele>
    <time>2014-04-26T07:17:16Z</time>
   </trkpt>
   <trkpt lat="54.3117780" lon="10.1093510">
    <ele>31.8</ele>
    <time>2014-04-26T07:17:17Z</time>
   </trkpt>
   <trkpt lat="54.3118120" lon="10.1092900">
    <ele>31.6</ele>
    <time>2014-04-26T07:17:19Z</time>
   </trkpt>
   <trkpt lat="54.3118400" lon="10.1092900">
    <ele>31.6</ele>
    <time>2014-04-26T07:17:20Z</time>
   </trkpt>
   <trkpt lat="54.3118710" lon="10.1093020">
    <ele>31.6</ele>
    <time>2014-04-26T07:17:21Z</time>
   </trkpt>
   <trkpt lat="54.3119030" lon="10.1093010">
    <ele>31.5</ele>
    <time>2014-04-26T07:17:22Z</time>
   </trkpt>
   <trkpt lat="54.3119350" lon="10.1093010">
    <ele>31.5</ele>
    <time>2014-04-26T07:17:23Z</time>
   </trkpt>
   <trkpt lat="54.3119700" lon="10.1093000">
    <ele>31.5</ele>
    <time>2014-04-26T07:17:24Z</time>
   </trkpt>
   <trkpt lat="54.3120060" lon="10.1092990">
    <ele>31.4</ele>
    <time>2014-04-26T07:17:25Z</time>
   </trkpt>
   <trkpt lat="54.3120440" lon="10.1092970">
    <ele>31.4</ele>
    <time>2014-04-26T07:17:26Z</time>
   </trkpt>
   <trkpt lat="54.3120850" lon="10.1092970">
    <ele>31.3</ele>
    <time>2014-04-26T07:17:27Z</time>
   </trkpt>
   <trkpt lat="54.3121460" lon="10.1092920">
    <ele>31.2</ele>
    <time>2014-04-26T07:17:28Z</time>
   </trkpt>
   <trkpt lat="54.3121870" lon="10.1092870">
    <ele>31.2</ele>
    <time>2014-04-26T07:17:29Z</time>
   </trkpt>
   <trkpt lat="54.3122290" lon="10.1092950">
    <ele>31.1</ele>
    <time>2014-04-26T07:17:30Z</time>
   </trkpt>
   <trkpt lat="54.3123290" lon="10.1093170">
    <ele>31.1</ele>
    <time>2014-04-26T07:17:32Z</time>
   </trkpt>
   <trkpt lat="54.3123760" lon="10.1093240">
    <ele>31.0</ele>
    <time>2014-04-26T07:17:33Z</time>
   </trkpt>
   <trkpt lat="54.3124250" lon="10.1093260">
    <ele>31.0</ele>
    <time>2014-04-26T07:17:34Z</time>
   </trkpt>
   <trkpt lat="54.3124740" lon="10.1093430">
    <ele>31.0</ele>
    <time>2014-04-26T07:17:35Z</time>
   </trkpt>
   <trkpt lat="54.3125190" lon="10.1093640">
    <ele>31.0</ele>
    <time>2014-04-26T07:17:36Z</time>
   </trkpt>
   <trkpt lat="54.3125640" lon="10.1093780">
    <ele>30.9</ele>
    <time>2014-04-26T07:17:37Z</time>
   </trkpt>
   <trkpt lat="54.3126660" lon="10.1093870">
    <ele>30.8</ele>
    <time>2014-04-26T07:17:39Z</time>
   </trkpt>
   <trkpt lat="54.3127230" lon="10.1093740">
    <ele>30.7</ele>
    <time>2014-04-26T07:17:40Z</time>
   </trkpt>
   <trkpt lat="54.3128310" lon="10.1093970">
    <ele>30.7</ele>
    <time>2014-04-26T07:17:42Z</time>
   </trkpt>
   <trkpt lat="54.3128840" lon="10.1094240">
    <ele>30.7</ele>
    <time>2014-04-26T07:17:43Z</time>
   </trkpt>
   <trkpt lat="54.3129550" lon="10.1094240">
    <ele>30.5</ele>
    <time>2014-04-26T07:17:44Z</time>
   </trkpt>
   <trkpt lat="54.3130140" lon="10.1094230">
    <ele>30.4</ele>
    <time>2014-04-26T07:17:45Z</time>
   </trkpt>
   <trkpt lat="54.3130940" lon="10.1094450">
    <ele>30.2</ele>
    <time>2014-04-26T07:17:46Z</time>
   </trkpt>
   <trkpt lat="54.3131450" lon="10.1094950">
    <ele>30.2</ele>
    <time>2014-04-26T07:17:48Z</time>
   </trkpt>
   <trkpt lat="54.3131900" lon="10.1095380">
    <ele>30.2</ele>
    <time>2014-04-26T07:17:49Z</time>
   </trkpt>
   <trkpt lat="54.3132410" lon="10.1095850">
    <ele>30.2</ele>
    <time>2014-04-26T07:17:50Z</time>
   </trkpt>
   <trkpt lat="54.3132730" lon="10.1096150">
    <ele>30.2</ele>
    <time>2014-04-26T07:17:51Z</time>
   </trkpt>
   <trkpt lat="54.3133170" lon="10.1096320">
    <ele>30.2</ele>
    <time>2014-04-26T07:17:52Z</time>
   </trkpt>
   <trkpt lat="54.3133530" lon="10.1096700">
    <ele>30.2</ele>
    <time>2014-04-26T07:17:53Z</time>
   </trkpt>
   <trkpt lat="54.3133990" lon="10.1096770">
    <ele>30.1</ele>
    <time>2014-04-26T07:17:54Z</time>
   </trkpt>
   <trkpt lat="54.3134360" lon="10.1096860">
    <ele>30.0</ele>
    <time>2014-04-26T07:17:55Z</time>
   </trkpt>
   <trkpt lat="54.3134710" lon="10.1097220">
    <ele>30.0</ele>
    <time>2014-04-26T07:17:56Z</time>
   </trkpt>
   <trkpt lat="54.3135070" lon="10.1097420">
    <ele>30.0</ele>
    <time>2014-04-26T07:17:57Z</time>
   </trkpt>
   <trkpt lat="54.3135530" lon="10.1097300">
    <ele>29.8</ele>
    <time>2014-04-26T07:17:59Z</time>
   </trkpt>
   <trkpt lat="54.3135790" lon="10.1097140">
    <ele>29.7</ele>
    <time>2014-04-26T07:18:00Z</time>
   </trkpt>
   <trkpt lat="54.3135930" lon="10.1097590">
    <ele>29.8</ele>
    <time>2014-04-26T07:18:04Z</time>
   </trkpt>
   <trkpt lat="54.3136030" lon="10.1097120">
    <ele>29.7</ele>
    <time>2014-04-26T07:18:36Z</time>
   </trkpt>
   <trkpt lat="54.3135640" lon="10.1096900">
    <ele>29.7</ele>
    <time>2014-04-26T07:18:57Z</time>
   </trkpt>
   <trkpt lat="54.3135590" lon="10.1096420">
    <ele>29.6</ele>
    <time>2014-04-26T07:19:05Z</time>
   </trkpt>
   <trkpt lat="54.3135800" lon="10.1095850">
    <ele>29.4</ele>
    <time>2014-04-26T07:19:09Z</time>
   </trkpt>
   <trkpt lat="54.3135980" lon="10.1095090">
    <ele>29.2</ele>
    <time>2014-04-26T07:19:11Z</time>
   </trkpt>
   <trkpt lat="54.3136070" lon="10.1094630">
    <ele>29.1</ele>
    <time>2014-04-26T07:19:12Z</time>
   </trkpt>
   <trkpt lat="54.3136170" lon="10.1094130">
    <ele>28.9</ele>
    <time>2014-04-26T07:19:13Z</time>
   </trkpt>
   <trkpt lat="54.3136270" lon="10.1093640">
    <ele>28.8</ele>
    <time>2014-04-26T07:19:14Z</time>
   </trkpt>
   <trkpt lat="54.3136380" lon="10.1093210">
    <ele>28.6</ele>
    <time>2014-04-26T07:19:15Z</time>
   </trkpt>
   <trkpt lat="54.3136550" lon="10.1092480">
    <ele>28.4</ele>
    <time>2014-04-26T07:19:17Z</time>
   </trkpt>
   <trkpt lat="54.3136770" lon="10.1092030">
    <ele>28.3</ele>
    <time>2014-04-26T07:19:20Z</time>
   </trkpt>
   <trkpt lat="54.3137060" lon="10.1092020">
    <ele>28.2</ele>
    <time>2014-04-26T07:19:24Z</time>
   </trkpt>
   <trkpt lat="54.3137390" lon="10.1092150">
    <ele>28.1</ele>
    <time>2014-04-26T07:19:34Z</time>
   </trkpt>
   <trkpt lat="54.3137760" lon="10.1092300">
    <ele>28.1</ele>
    <time>2014-04-26T07:19:36Z</time>
   </trkpt>
   <trkpt lat="54.3138040" lon="10.1092430">
    <ele>28.0</ele>
    <time>2014-04-26T07:19:37Z</time>
   </trkpt>
   <trkpt lat="54.3138370" lon="10.1092430">
    <ele>27.9</ele>
    <time>2014-04-26T07:19:38Z</time>
   </trkpt>
   <trkpt lat="54.3138670" lon="10.1092180">
    <ele>27.7</ele>
    <time>2014-04-26T07:19:39Z</time>
   </trkpt>
   <trkpt lat="54.3138820" lon="10.1091730">
    <ele>27.5</ele>
    <time>2014-04-26T07:19:40Z</time>
   </trkpt>
   <trkpt lat="54.3138750" lon="10.1090930">
    <ele>27.4</ele>
    <time>2014-04-26T07:19:42Z</time>
   </trkpt>
   <trkpt lat="54.3138540" lon="10.1090440">
    <ele>27.4</ele>
    <time>2014-04-26T07:19:44Z</time>
   </trkpt>
   <trkpt lat="54.3138540" lon="10.1089820">
    <ele>27.2</ele>
    <time>2014-04-26T07:19:46Z</time>
   </trkpt>
   <trkpt lat="54.3138810" lon="10.1089480">
    <ele>27.1</ele>
    <time>2014-04-26T07:19:48Z</time>
   </trkpt>
   <trkpt lat="54.3138850" lon="10.1088960">
    <ele>27.0</ele>
    <time>2014-04-26T07:19:51Z</time>
   </trkpt>
   <trkpt lat="54.3138880" lon="10.1088440">
    <ele>26.9</ele>
    <time>2014-04-26T07:19:54Z</time>
   </trkpt>
   <trkpt lat="54.3138500" lon="10.1088150">
    <ele>26.9</ele>
    <time>2014-04-26T07:19:57Z</time>
   </trkpt>
   <trkpt lat="54.3138190" lon="10.1088190">
    <ele>27.0</ele>
    <time>2014-04-26T07:19:59Z</time>
   </trkpt>
   <trkpt lat="54.3137840" lon="10.1088280">
    <ele>27.1</ele>
    <time>2014-04-26T07:20:04Z</time>
   </trkpt>
   <trkpt lat="54.3138040" lon="10.1087920">
    <ele>27.0</ele>
    <time>2014-04-26T07:20:18Z</time>
   </trkpt>
   <trkpt lat="54.3138230" lon="10.1088260">
    <ele>27.0</ele>
    <time>2014-04-26T07:21:05Z</time>
   </trkpt>
  </trkseg>
 </trk>
</gpx>';

        $gpxc = new GPXConverter();
        $gpxc->loadContentFromString($gpx);
        $gpxc->parseContent();
        $pathArray = $gpxc->getPathArray();

        $tile = new Tile();
        $tile->generatePlaceByLatitudeLongitudeZoom(54.3119, 10.1260, 13);

        $tile->dropPathArray($pathArray);
        $tile->sortPixelList();

        $tp = new PNGTilePrinter($tile);
        $tp->printTile();

        $response = new Response();
        $response->setContent($tp->getImageFileContent());
        $response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
