<p align="center" style="padding:20px;"><img src="https://www.decampoacampo.com/__dcac/application/views/landing/img/logo.svg" alt="De Campo a Campo" width="180" /></p>

## Instalación

<p>Ejecutar los siguientes comandos:</p>

<ol>
<li>git clone https://github.com/JavierVeron/decampoacampo.git</li>
<li>cd decampoacampo</li>
<li>docker-compose up -d --build</li>
</ol>

## Endpoints

<pre>
<p>http://localhost:8080/api/productos<br>
Method: <b>GET</b></p>
</pre>

<pre>
<p>http://localhost:8080/api/productos/{id}<br>
Method: <b>GET</b><br>
Parameters:<br>
<b>id</b> => integer</p>
</pre>

<pre>
<p>http://localhost:8080/api/productos<br>
Method: <b>POST</b><br>
Body Parameters (JSON):<br>
<b>nombre</b> => string<br>
<b>descripcion</b> => string<br>
<b>precio</b> => float</p>
</pre>

<pre>
<p>http://localhost:8080/api/productos/2<br>
Method: <b>PUT</b><br>
Parameters:<br>
<b>id</b> => integer<br>
Body Parameters (JSON):<br>
<b>nombre</b> => string<br>
<b>descripcion</b> => string<br>
<b>precio</b> => float</p>
</pre>

<pre>
<p>http://localhost:8080/api/productos/{id}<br>
Method: <b>DELETE</b><br>
Parameters:<br>
<b>id</b> => integer</p>
</pre>