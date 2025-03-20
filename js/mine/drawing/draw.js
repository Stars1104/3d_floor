//***************************************************************************************//
//
//	FabricJS Object Drawing file
//	Created By Giryong Jong. 1/23/2014
//
//***************************************************************************************//

var drawObj = function () {
	var main = this;

	main.canvasID = "canvas";
	main.canvWidth = 300;
	main.canvHeight = 300;
	main.canvas = null;

	main.prevScale = 1;
	main.unit = 50; 	// 1 metre = 50pixel;
	main.sel_obj = null;
	main.mainObj = null;

	main.init = function (width, height, mainObj) {
		main.canvWidth = width;
		main.canvHeight = height;
		main.mainObj = mainObj;

		main.canvasCSS();
		main.initFabric();
		main.drawEvent();
		main.objectEvent();
		main.keyEvent();
	}

	main.canvasCSS = function () {
		$("#" + main.canvasID).attr("width", main.canvWidth);
		$("#" + main.canvasID).attr("height", main.canvHeight);
		$("#" + main.canvasID).css("width", main.canvWidth);
		$("#" + main.canvasID).css("height", main.canvHeight);

		if (main.canvas) {
			main.canvas.setWidth(main.canvWidth);
			main.canvas.setHeight(main.canvHeight);
			main.canvas.renderAll();
			main.canvas.calcOffset();
		}
	}

	main.initFabric = function () {
		main.canvas = new fabric.Canvas(main.canvasID);
	}

	main.objectEvent = function () {
		main.setObjProperty();
		main.getObjProperty();
	}

	main.canvasToJson = function () {
		var objArr = new Array();
		var groupdID = 1;

		var fwidth = $("#txt_fwidth").val();
		var fdepth = $("#txt_fdepth").val();
		var bcolor = rgb2hex($("#txt_fbgcolor").css("background-color"));
		var bimage = $("#txt_fbgimage").val();
		var sleft = $("#slider_body a").css('left');

		var min_top = 9999999;
		var min_left = 9999999;

		/* floor infomation */
		objArr.push({ type: "floor", width: fwidth, height: fdepth, bcolor: bcolor, bimage: bimage, canvScale: main.prevScale, sleft: sleft });

		main.canvas.forEachObject(function (group, i) {
			if (group.get("type") == "group") {
				min_top = 9999999;
				min_left = 9999999;

				group.forEachObject(function (cgroup) {
					min_top = Math.min(cgroup.top, min_top);
					min_left = Math.min(cgroup.left, min_left);
				});

				group.forEachObject(function (cgroup) {
					objArr = main.objToJson(objArr, cgroup, groupdID, group, min_top, min_left);
				});

				groupdID++;
			}
			else {
				objArr = main.objToJson(objArr, group);
			}
		});

		return JSON.stringify(objArr);
	}

	main.objToJson = function (objArr, group, groupdID, pgroup, mtop, mleft) {
		var rate = main.prevScale;

		group.forEachObject(function (object, i) {
			if (object.get("type") == "number") return;

			var obj_path = "";
			var type = object.get("type");
			var x = group.left * (1 / rate);
			var y = group.top * (1 / rate);
			var width = group.getWidth() * (1 / rate);
			var height = group.getHeight() * (1 / rate);
			var depth = group._objects[0].get("depth");
			var color = object.get("fill");
			var angle = group.get("angle");
			var orgPos = null;
			var points = object.get("points");
			var imgUrl = object.get("url");
			var descr = object.get("descr");
			var gNo = group._objects[1].get("text");
			var text = group._objects[0].get('text');
			var mtl = object.get("mtl");
			var obj_dir = object.get("obj_dir");

			if (pgroup) {
				x = pgroup.left + group.left - mleft;
				y = pgroup.top + group.top - mtop;
			}

			if (angle != 0) {
				orgPos = main.getOrgPos(group);
				x = orgPos.x;
				y = orgPos.y;
			}

			if (object.type == 'image') {
				obj_path = object.get("obj3d");
				objArr.push({ obj: obj_path, mtl: mtl, type: type, x: x, y: y, width: width, height: height, depth: depth, color: color, angle: angle, points: null, url: imgUrl, gid: groupdID, gNo: gNo, descr: descr, obj_dir: obj_dir });
			}
			else {
				objArr.push({ obj: null, mtl: mtl, type: type, x: x, y: y, width: width, height: height, depth: depth, color: color, angle: angle, points: points, url: imgUrl, gid: groupdID, gNo: gNo, descr: descr, text: text });
			}
		});

		return objArr;
	}

	main.jsonToCanvas = function (json) {
		var obj = JSON.parse(json);

		if (obj.length == 0) return;

		var size = "";
		var url_3d = "";
		var url_2d = "";
		var color = "#fff";
		var rate = obj[0]['canvScale'];
		var angle = 0;
		var gID = 0;
		var text = "";
		var mtl = "";
		var sleft = obj[0]['sleft'];

		main.canvas.clear().renderAll();
		main.prevScale = rate;

		$('#grid_bg').css('background-color', obj[0]['bcolor']);
		$('#txt_fbgcolor').css('backgroundColor', obj[0]['bcolor']);
		$("#slider_body a").css('left', sleft);

		/* init objects */

		for (var i = 1; i < obj.length; i++) {
			if (obj[i]['type'] == "floor") continue;

			url_2d = obj[i]['url'];
			url_3d = obj[i]['obj'];
			mtl = obj[i]['mtl'];
			color = obj[i]['color'];
			angle = obj[i]['angle'];
			gID = obj[i]['gid'];
			size = obj[i]['width'] / main.unit + "," + obj[i]['height'] / main.unit + "," + obj[i]['depth'] / main.unit;
			gNo = obj[i]['gNo'];
			descr = obj[i]['descr'];
			text = obj[i]['text'];
			obj_dir = obj[i]['obj_dir'];

			if (url_2d) url_2d = url_2d.replace("objs/" + obj_dir + "/", "");

			main.addObject(obj[i]['type'], obj[i]['x'] * rate, obj[i]['y'] * rate, url_2d, size, url_3d, mtl, color, angle, gID, gNo, descr, text, obj_dir);
		}

		/* init floor */
		main.mainObj.canvWidth = obj[0]['width'] * main.unit;
		main.mainObj.canvHeight = obj[0]['height'] * main.unit;
		main.mainObj.rate = obj[0]['canvScale'];
		main.mainObj.initCSS();

		// main.areaScale(obj[0]['canvScale']);

		setTimeout(main.rGroup, 300);
	}

	main.getObjProperty = function () {
		main.canvas.on("selection:cleared", function (options) {
			main.sel_obj = null;

			$("#txt_descr").val("Floor");
			$("#txt_depth").val("0");
			$("#txt_width").val("0");
			$("#txt_height").val("0");
			$("#txt_angle").val("0");

			$("#obj_prop").css("display", "none");
			$("#floor_prop").css("display", "");
		});

		main.canvas.on("object:selected", function (options) {
			var obj = options.target;
			var descr = obj._objects[0].get("descr");
			var depth = obj._objects[0].get("depth");
			var objNo = obj._objects[1].get("text");
			var width = obj.getWidth();
			var height = obj.getHeight();
			var angle = obj.getAngle();
			var color = obj.backgroundColor;
			var text = "";

			main.sel_obj = obj;

			if (!depth) depth = 0;

			if (main.sel_obj._objects[0].type == "text") {
				text = main.sel_obj._objects[0].get('text');
				$("#txt_txtval").val(text);
				$("#txt_prop").css({ "display": "table-row" });
			}
			else $("#txt_prop").css("display", "none");

			$("#txt_descr").val(descr);
			$("#txt_depth").val(height / main.unit / main.prevScale);
			$("#txt_width").val(width / main.unit / main.prevScale);
			$("#txt_height").val(depth / main.unit);
			$("#txt_angle").val(angle);
			$("#txt_bgcolor").css("background-color", color);
			$("#txt_number").val(objNo);
			$("#obj_prop").css("display", "flex");
			$("#floor_prop").css("display", "none");

			var Width = (width / main.unit / main.prevScale).toFixed(1);
			var Height = (height / main.unit / main.prevScale).toFixed(1);
			var Depth = (depth / main.unit).toFixed(1);

			var Size = { width: Width, height: Height, depth: Depth };

			localStorage.removeItem("Size");

			localStorage.setItem("Size", JSON.stringify(Size));
		});

		main.canvas.on("object:modified", function (option) {
			const obj = option.target;

			var depth = obj._objects[0].get("depth");
			var width = obj.getWidth();
			var height = obj.getHeight();

			var Width = (width / main.unit / main.prevScale).toFixed(1);
			var Height = (height / main.unit / main.prevScale).toFixed(1);
			var Depth = (depth / main.unit).toFixed(1);

			var Size = { width: Width, height: Height, depth: Depth };

			localStorage.removeItem("Size");

			localStorage.setItem("Size", JSON.stringify(Size));
		})
	}

	main.setObjProperty = function () {
		$("#txt_descr").keyup(function () {
			if (!main.sel_obj) return;

			main.sel_obj._objects[0].set("descr", $(this).val());
		});

		$("#txt_height").keyup(function () {
			if (!main.sel_obj) return;

			main.sel_obj._objects[0].set("depth", $(this).val() * main.unit);
		});

		$("#txt_width").keyup(function () {
			if (!main.sel_obj) return;

			main.sel_obj.width = $(this).val() * main.unit;

			main.sel_obj._objects[0].left = $(this).val() * main.unit / 2 * (-1);
			main.sel_obj._objects[0].width = $(this).val() * main.unit;

			main.sel_obj.setCoords();
			main.canvas.renderAll();
			main.canvas.calcOffset();
		});

		$("#txt_depth").keyup(function () {
			if (!main.sel_obj) return;

			main.sel_obj.height = $(this).val() * main.unit;

			main.sel_obj._objects[0].top = $(this).val() * main.unit / 2 * (-1);
			main.sel_obj._objects[0].height = $(this).val() * main.unit;

			main.sel_obj.setCoords();
			main.canvas.renderAll();
			main.canvas.calcOffset();
		});

		$("#txt_angle").keyup(function () {
			if (!main.sel_obj) return;

			main.sel_obj.angle = $(this).val() * 1;
			main.sel_obj.setCoords();
			main.canvas.renderAll();
			main.canvas.calcOffset();
		});

		$("#txt_txtval").keyup(function () {
			if (!main.sel_obj) return;

			main.sel_obj._objects[0].set("text", $(this).val());
			main.sel_obj._objects[0].left = main.sel_obj._objects[0].getWidth() / (-2);
			main.sel_obj.width = main.sel_obj._objects[0].getWidth();
			main.canvas.renderAll();
			main.canvas.calcOffset();
		});

		$("#txt_number").keyup(function () {
			main.sel_obj._objects[1].set("text", $(this).val());

			if ($(this).val() == 0) {
				main.sel_obj._objects[1].set("visible", false);
			}
			else {
				main.sel_obj._objects[1].set("visible", true);
			}

			main.canvas.renderAll();
		});
	}

	main.areaScale = function (rate) {
		var scaleN = rate / main.prevScale;
		var scaleX = 1;
		var scaleY = 1;

		main.canvas.forEachObject(function (object, i) {
			scaleX = object.scaleX;
			scaleY = object.scaleY;

			object.top = object.top * scaleN;
			object.left = object.left * scaleN;

			object.scaleX = scaleX * scaleN;
			object.scaleY = scaleY * scaleN;

			object.setCoords();
		});


		main.prevScale = rate;
		main.canvasCSS();
	}

	main.drawEvent = function () {
		$("#" + main.canvasID).droppable(
			{
				drop: function (event, ui) {
					var tool = $(ui.draggable).attr('tool');
					var tleft = $("#canvas_area").css('left').replace("px", "") * 1;
					var ttop = $("#canvas_area").css('top').replace("px", "") * 1;

					var left = ui.helper.offset().left - tleft;
					var top = ui.helper.offset().top - ttop - 50;

					var twod = $(ui.draggable).attr('twod');
					var size = $(ui.draggable).attr('size');
					var thrd = $(ui.draggable).attr('thrd');
					var mtl = $(ui.draggable).attr('mtl');
					var dir = $(ui.draggable).attr('name');

					main.addObject(tool, left, top, twod, size, thrd, mtl, "none", 0, 0, 0, "", "", dir);
				}
			});
	}

	main.RemoveOBJ = function () {
		var active_obj = main.canvas.getActiveObject();
		main.canvas.remove(active_obj);
		main.canvas.renderAll();
	}

	main.keyEvent = function () {
		$(document).on("keydown", function (evt) {
			var active_obj = main.canvas.getActiveObject();

			if (!active_obj)
				active_obj = main.canvas.getActiveGroup();

			if (!active_obj) return;

			switch (evt.keyCode) {
				case 46: 	// delete key
					main.canvas.remove(active_obj);
					break;

				case 188: 	// < key
					active_obj.left = active_obj.left - 1;
					main.canvas.renderAll();
					break;

				case 190: 	// > key
					active_obj.left = active_obj.left + 1;
					main.canvas.renderAll();
					break;
			}
		});
	}

	main.addObject = function (tool, left, top, twod, size, thrd, mtl, effect_color, angle, groupID, groupNo, descr, text, obj_dir) {
		if (tool == "floor") return;

		var color = "#fff";
		var width = 150;
		var height = 100;
		var depth = 0;
		var sArr = new Array();
		var nColor = "#000";
		var gnum = "0";
		var visible = 0;

		if (effect_color) color = effect_color;
		if (effect_color == "none") color = "#fff";
		if (color == "#000") nColor = "#fff";

		if (size) sArr = size.split(",");
		if (sArr[0]) width = sArr[0] * 1 * main.unit;
		if (sArr[1]) height = sArr[1] * 1 * main.unit;
		if (sArr[2]) depth = sArr[2] * 1 * main.unit;
		if (groupNo && groupNo != 0) {
			visible = 1;
			gnum = groupNo;
		}

		switch (tool) {
			case "text":
				if (effect_color == "" || effect_color == "none") color = "#000";
				if (!text) text = "Add Text";

				var number = new fabric.Text(gnum,
					{
						type: 'number',
						left: 0,
						top: 0,
						fill: nColor,
						visible: visible,
						fontSize: 20
					});

				number.top = (height - number.height) / 2;
				number.left = (width - number.width) / 2;

				var object = new fabric.Text(text,
					{
						left: 0,
						top: 0,
						fill: color,
						angle: 0,
						depth: depth,
						gID: groupID,
						type: tool,
						size: size,
						descr: descr
					});

				var group = new fabric.Group([object, number],
					{
						type: "basic_group",
						left: left,
						top: top,
						angle: 0,
						scaleX: main.prevScale,
						scaleY: main.prevScale,
						centeredRotation: true
					});

				main.canvas.add(group);
				rotateObject(group, angle);

				break;

			case "line":
				if (!size) height = 10;

				var number = new fabric.Text(gnum,
					{
						type: 'number',
						left: 0,
						top: 0,
						fill: nColor,
						visible: visible,
						fontSize: 20
					});

				number.top = (height - number.height) / 2;
				number.left = (width - number.width) / 2;

				var object = new fabric.Rect(
					{
						type: tool,
						left: 0,
						top: 0,
						width: width,
						height: height,
						fill: color,
						angle: 0,
						stroke: 1,
						depth: depth,
						borderColor: "black",
						hasBorders: true,
						gID: groupID,
						size: size,
						descr: descr
					});

				var group = new fabric.Group([object, number],
					{
						type: "basic_group",
						left: left,
						top: top,
						angle: 0,
						width: width,
						height: height,
						scaleX: main.prevScale,
						scaleY: main.prevScale,
						centeredRotation: true
					});

				main.canvas.add(group);
				rotateObject(group, angle);
				break;

			case "rect":
				var number = new fabric.Text(gnum,
					{
						type: 'number',
						left: 0,
						top: 0,
						fill: nColor,
						visible: visible,
						fontSize: 20

					});

				number.top = (height - number.height) / 2;
				number.left = (width - number.width) / 2;

				var object = new fabric.Rect(
					{
						type: tool,
						left: 0,
						top: 0,
						width: width,
						height: height,
						fill: color,
						angle: 0,
						depth: depth,
						stroke: 2,
						borderColor: "black",
						hasBorders: true,
						gID: groupID,
						size: size,
						descr: descr,
					});

				var group = new fabric.Group([object, number], {
					type: "basic_group",
					left: left,
					top: top,
					angle: 0,
					scaleX: main.prevScale,
					scaleY: main.prevScale
				});

				main.canvas.add(group);
				rotateObject(group, angle);
				break;

			case "triangle":
				if (!size) {
					width = 100;
					height = 100;
				}

				var number = new fabric.Text(gnum,
					{
						type: 'number',
						left: 0,
						top: 0,
						fill: nColor,
						visible: visible,
						fontSize: 20
					});

				number.top = width / 2 - number.height / 2 + 5;
				number.left = height / 2 - number.width / 2;

				var object = new fabric.Triangle(
					{
						type: tool,
						left: 0,
						top: 0,
						width: width,
						height: height,
						depth: depth,
						fill: color,
						angle: 0,
						stroke: 2,
						borderColor: "black",
						hasBorders: true,
						gID: groupID,
						size: size,
						descr: descr
					});

				var group = new fabric.Group([object, number], {
					type: "basic_group",
					left: left,
					top: top,
					angle: 0,
					scaleX: main.prevScale,
					scaleY: main.prevScale
				});

				main.canvas.add(group);
				rotateObject(group, angle);
				break;

			case "circle":
				var radius = 100;
				var x_scale = 1;
				var y_scale = 1;

				if (size) {
					radius = Math.min(width, height);
					x_scale = width / radius;
					y_scale = height / radius;
				}

				var number = new fabric.Text(gnum,
					{
						type: 'number',
						left: radius / 2 - 5,
						top: radius / 2 - 15,
						fill: nColor,
						visible: visible,
						fontSize: 20

					});

				number.top = radius / 2 - number.height / 2;
				number.left = radius / 2 - number.width / 2;

				var object = new fabric.Circle(
					{
						type: tool,
						left: 0,
						top: 0,
						radius: radius / 2,
						fill: color,
						angle: 0,
						stroke: 2,
						borderColor: "black",
						hasBorders: true,
						depth: depth,
						gID: groupID,
						size: size,
						descr: descr
					});

				var group = new fabric.Group([object, number], {
					type: "basic_group",
					left: left,
					top: top,
					angle: 0,
					scaleX: main.prevScale,
					scaleY: main.prevScale
				});

				main.canvas.add(group);
				rotateObject(group, angle);
				break;

			case "star":
				var lwidth = 100;
				var swidth = 40;
				var length = 0;
				var sangle = 0;
				var x, y;
				var starPoints = [];

				var number = new fabric.Text(gnum,
					{
						type: 'number',
						left: 0,
						top: 0,
						fill: nColor,
						visible: visible,
						fontSize: 20
					});

				for (var i = 0; i < 10; i++) {
					if (i % 2 == 0) length = lwidth;
					else length = swidth;

					sangle = 2 * Math.PI / 10 * i;

					x = left - Math.sin(sangle) * length;
					y = top - Math.cos(sangle) * length;

					starPoints.push({ x: x, y: y });
				}

				var object = new fabric.Polygon(starPoints, {
					type: tool,
					left: 0,
					top: 0,
					fill: color,
					stroke: 2,
					borderColor: "black",
					hasBorders: true,
					angle: angle,
					depth: depth,
					gID: groupID,
					points: starPoints,
					size: size,
					descr: descr
				});

				number.top = (object.height - number.height) / 2;
				number.left = (object.width - number.width) / 2;

				var group = new fabric.Group([object, number], {
					type: "basic_group",
					left: left,
					top: top,
					angle: 0,
					scaleX: main.prevScale,
					scaleY: main.prevScale
				});

				main.canvas.add(group);
				rotateObject(group, angle);
				break;

			case "ellipse":
				if (!size) {
					width = 100;
					height = 60;
				}

				var number = new fabric.Text(gnum,
					{
						type: 'number',
						left: width / 2 - 5,
						top: height / 2 - 15,
						fill: nColor,
						visible: visible,
						fontSize: 20
					});

				var object = new fabric.Ellipse(
					{
						type: tool,
						left: 0,
						top: 0,
						rx: width / 2,
						ry: height / 2,
						fill: color,
						angle: 0,
						stroke: 2,
						depth: depth,
						borderColor: "black",
						hasBorders: true,
						size: size,
						descr: descr,
						gID: groupID
					});

				var group = new fabric.Group([object, number], {
					type: "basic_group",
					left: left,
					top: top,
					angle: 0,
					scaleX: main.prevScale,
					scaleY: main.prevScale
				});

				main.canvas.add(group);
				rotateObject(group, angle);
				break;

			case "image":
				var url = "objs/" + obj_dir + "/" + twod;
				var obj3d = thrd;

				fabric.Image.fromURL(url, function (img) {
					img.set({
						width: sArr[0] * main.unit,
						height: sArr[1] * main.unit,
					});

					if (effect_color && effect_color !== "none") {
						img.filters.push(new fabric.Image.filters.Tint({
							color: color,
							opacity: 0.6
						}));

						img.applyFilters();
					}

					img.clone(function (clonedImg) {
						var number = new fabric.Text(gnum, {
							type: 'number',
							fill: nColor,
							fontSize: 20,
							visible: visible
						});

						setTimeout(() => {
							number.set({
								left: (clonedImg.width - number.width) / 2,
								top: (clonedImg.height - number.height) / 2
							});

							var object = clonedImg.set({
								type: tool,
								left: 0,
								top: 0,
								url: url,
								twod: twod,
								width: sArr[0] * main.unit,
								height: sArr[1] * main.unit,
								depth: sArr[2] * main.unit,
								obj3d: obj3d,
								mtl: mtl,
								angle: 0,
								gID: groupID,
								size: size,
								descr: descr,
								fill: effect_color,
								obj_dir: obj_dir,
								backgroundColor: effect_color
							});

							var group = new fabric.Group([object, number], {
								type: "basic_group",
								left: left,
								top: top,
								angle: 0,
								scaleX: main.prevScale,
								scaleY: main.prevScale
							});

							main.canvas.add(group);
							rotateObject(group, angle);
							main.canvas.renderAll();
						}, 50);
					});
				});
				break
		}

		main.getOrgPos = function (object) {
			var rate = main.prevScale;
			var x = object.get("left") * (1 / rate);
			var y = object.get("top") * (1 / rate);
			var width = object.getWidth() * (1 / rate);
			var height = object.getHeight() * (1 / rate);
			var mangle = object.get("angle") * (Math.PI / 180) * (-1);

			var angle1 = Math.atan(width / height);
			var angle2 = (180 * (Math.PI / 180) - mangle) / 2;
			var radian = angle2 - angle1;

			var r1 = Math.sqrt(Math.pow(width / 2, 2) + Math.pow(height / 2, 2));
			var l1 = Math.sin(mangle / 2) * r1;
			var r2 = l1 * 2;

			var dy = Math.cos(radian) * r2;
			var dx = Math.sin(radian) * r2;

			var nx = x + dx;
			var ny = y - dy;

			return ({ x: nx, y: ny });
		}

		main.removeOBJ = function () {
			var group = main.canvas.getActiveGroup();

			if (group) {
				group.forEachObject(function (obj) {
					main.canvas.remove(obj);
				});

				main.canvas.remove(group);
				main.canvas.deactivateAll().renderAll();
			}

			if (main.canvas.getActiveObject())
				main.canvas.remove(main.canvas.getActiveObject());

			main.canvas.renderAll();
		}

		main.cloneOBJ = function () {
			if (main.canvas.getActiveGroup()) {
				var bgroup = main.canvas.getActiveGroup();

				bgroup.forEachObject(function (group) {
					var target = group._objects[0];
					var gnum = group._objects[1].text;

					var top = bgroup.top + group.top - 10;
					var left = bgroup.left + group.left - 10;

					main.addObject(target.type, left, top, target.twod, target.size, target.obj3d, target.mtl, target.fill, group.angle, target.gID, gnum, "", "", target.obj_dir);
				});
			}

			if (main.canvas.getActiveObject()) {
				var group = main.canvas.getActiveObject();
				var target = group._objects[0];
				var gnum = group._objects[1].text;

				const storedSize = JSON.parse(localStorage.getItem("Size") || "{}");

				target.size = storedSize.width + "," + storedSize.height + "," + storedSize.depth;

				main.addObject(target.type, group.left - 10, group.top - 10, target.twod, target.size, target.obj3d, target.mtl, target.fill, group.angle, target.gID, gnum, "", "", target.obj_dir);
			}

			main.canvas.renderAll();
		}

		main.mGroup = function () {
			if (main.canvas.getActiveGroup()) {
				var sel_group = main.canvas.getActiveGroup(),
					new_group = new fabric.Group();

				var left = 99999, top = 99999;

				main.canvas.deactivateAll().renderAll();

				sel_group.forEachObject(function (obj) {
					top = Math.min(top, obj.top);
					left = Math.min(left, obj.left);

					if (obj.type == "group") {
						var subX = 99999;
						var subY = 99999;

						for (var i = 0; i < obj._objects.length; i++) {
							subX = Math.min(subX, obj._objects[i].left);
							subY = Math.min(subY, obj._objects[i].top);
						}

						obj.forEachObject(function (child) {
							child.top = obj.top - sel_group.top - (subY - child.top)
							child.left = obj.left - sel_group.left - (subX - child.left)

							child.hasBorders = false;
							new_group.addWithUpdate(child);
						});
					}
					else {
						obj.top = obj.top - sel_group.top;
						obj.left = obj.left - sel_group.left;

						obj.hasBorders = false;
						new_group.addWithUpdate(obj);
					}

					main.canvas.remove(obj);
				});

				new_group.set({ left: left, top: top });
				main.canvas.add(new_group);
				main.canvas.setActiveObject(new_group);

				main.canvas.renderAll();
				main.canvas.calcOffset();
			}
		}

		main.rGroup = function () {
			var objArr = new Array();
			var gID = 0;
			var gleft = 99999;
			var gtop = 99999;

			main.canvas.renderAll();
			main.canvas.forEachObject(function (object, i) {
				if (object._objects[0].get("gID") && object._objects[0].get("gID") != 0) {
					if (gID == object._objects[0].get("gID")) {
						objArr.push(object);

						gtop = Math.min(gtop, object.get("top"));
						gleft = Math.min(gleft, object.get("left"));
					}
					else {
						if (objArr.length > 0) {
							for (var i = 0; i < objArr.length; i++) {
								var top = objArr[i].get('top') - gtop;
								var left = objArr[i].get('left') - gleft;

								objArr[i].set({ left: left, top: top });
							}

							main.canvas.add(new fabric.Group(objArr, {
								left: gleft,
								top: gtop
							}));
						}

						gID = object._objects[0].get("gID");
						gleft = object.get("left");
						gtop = object.get("top");

						objArr = new Array();
						objArr.push(object);
					}

					main.canvas.remove(object);
				}
			});

			if (objArr.length > 0) {
				for (var i = 0; i < objArr.length; i++) {
					var top = objArr[i].get('top') - gtop;
					var left = objArr[i].get('left') - gleft;

					objArr[i].set({ left: left, top: top });
				}

				main.canvas.add(new fabric.Group(objArr, {
					left: gleft,
					top: gtop
				}));
			}
		}

		main.uGroup = function () {
			if (main.canvas.getActiveObject()) {
				var group = main.canvas.getActiveObject();
				var subX = 99999;
				var subY = 99999;

				for (var i = 0; i < group._objects.length; i++) {
					subX = Math.min(subX, group._objects[i].left);
					subY = Math.min(subY, group._objects[i].top);
				}

				if (group.type == "group") {
					group.forEachObject(function (object) {
						var target = object._objects[0];
						var gnum = object._objects[1].text;

						var top = group.top - (subY - object.top);
						var left = group.left - (subX - object.left);

						main.addObject(target.type, left, top, target.twod, target.size, target.obj3d, target.mtl, target.fill, object.angle, target.gID, gnum, "", "", target.obj_dir);
					});

					main.canvas.remove(group);
				}
			}
		}
	}
};