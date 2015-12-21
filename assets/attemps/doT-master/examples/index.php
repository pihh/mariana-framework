<html>
	<head>

	<script id="headertmpl" type="text/x-dot-template">
		<h1>{{=it.title}}</h1>
	</script>

	<script id="pagetmpl" type="text/x-dot-template">
		<h2>Here is the page using a header template</h2>
		{{#def.header}}
		{{=it.name}}
	</script>

	<script id="customizableheadertmpl" type="text/x-dot-template">
		 {{#def.header}}
		 {{#def.mycustominjectionintoheader || ''}}
	 </script>

	<script id="pagetmplwithcustomizableheader" type="text/x-dot-template">
		<h2>Here is the page with customized header template</h2>
		{{##def.mycustominjectionintoheader:
			<div>{{=it.title}} is not {{=it.name}}</div>
		#}}
		{{#def.customheader}}
		{{=it.name}}
		<input type="text" value="{{=it.name}}">
	</script>

	<script src="doT.min.js" type="text/javascript"></script>
	</head>

	<body>
		<div id="content"></div>
		<div id="contentcustom"></div>
	</body>

	<script type="text/javascript">
		var def = {
			header: document.getElementById('headertmpl').text,
			customheader: document.getElementById('customizableheadertmpl').text
		};
		var data = {
			title: "My title",
			name: "My name"
		};

		var pagefn = doT.template(document.getElementById('pagetmpl').text, undefined, def);
		document.getElementById('content').innerHTML = pagefn(data);

		pagefn = doT.template(document.getElementById('pagetmplwithcustomizableheader').text, undefined, def);
		document.getElementById('contentcustom').innerHTML = pagefn(data);

	</script>

</html>
