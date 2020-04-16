/* 
 * Image Zooming Library using HTML5 Canvas
 * 
 * @author: Joey Takeda, but with resources from 
 * 
 * 
 */
 
 window.addEventListener('load',init);
var init = 0;



 
 function init(){
    document.querySelectorAll('a.zoomable').forEach(function(i){
        i.addEventListener('click',function(e){
            e.preventDefault();
            drawFacs(i);
        });
    });
    document.querySelectorAll('.facsCloser').forEach(function(closer){
        closer.addEventListener('click', function(e){
            e.preventDefault();
            closeViewer();
        });
    });
    var canvas = document.getElementsByTagName('canvas')[0];
    canvas.setAttribute('data-width', canWidth);
    canvas.setAttribute('data-height', canHeight);
 }
 
function closeViewer(){
        var body = document.getElementsByTagName('body')[0];
        body.classList.remove('facs-overlay');
        var fcv = document.getElementById('facsViewerContainer');
        fcv.classList.remove('open');
        fcv.classList.remove('loaded');
        if (window.stop !== undefined){
            window.stop();
         }
        else if (document.execCommand !== undefined){
                document.execCommand("Stop", false);
        }
        
}
 

function drawFacs(link){
    var imgDiv = link;
    console.log('Drawing');
    var facs = new Image;
    var imgLink = imgDiv.getAttribute('href');
    imgSrc = imgLink;
    var canvas = document.getElementsByTagName('canvas')[0];
    
    var innerWidth = (window.innerWidth);
    var facsScale = 0.75;
    canvas.width = innerWidth < 800 ? innerWidth * facsScale : 800;
    canvas.height = innerWidth < 800 ? innerWidth * facsScale * 0.75 : 600;
    refreshCanvas(canvas);
    var ctx = canvas.getContext("2d");
    var facsCtr = document.getElementById('facsViewerContainer');
    document.getElementsByTagName('body')[0].classList.add('facs-overlay');
    facsCtr.classList.add('open');
    /* Add a little loading boilerplate... */
    
    /* Once the facs loads, then draw the facsimile */
    facs.onload = function(){
       facsCtr.classList.add('loaded');
       drawFacsimile(imgDiv, this);
    }
    facs.onerror = function(){
        drawErrorMessage();
    }
    facs.src = imgSrc
    ctx.zoomLevel = 1;
}

function drawErrorMessage(){
    var canvas = document.getElementsByTagName('canvas')[0];
    var ctx = canvas.getContext("2d");
        ctx.clearRect(0,0,canvas.width,canvas.height);
	    ctx.restore();
        ctx.font = '20px serif';
        ctx.fillStyle = "white";
        ctx.fillText("No image available...", 25, 30);
        console.log(ctx);
}

function refreshCanvas(canvas){
    var newCanvas = canvas.cloneNode(true);
    canvas.parentNode.replaceChild(newCanvas, canvas);
}



function drawFacsimile(imgDiv, facs){
   /* Get the canvas */
    var viewer = document.getElementById('facsViewerContainer');
    var canvas = viewer.getElementsByTagName('canvas')[0];
	var ctx = canvas.getContext('2d');
    trackTransforms(ctx);
	function redraw(){
	       ctx.save();
		   ctx.setTransform(1,0,0,1,0,0);
		   ctx.clearRect(0,0,canvas.width,canvas.height);
	       ctx.restore();
	       var rw = canvas.width / facs.naturalWidth;
	       var rh = canvas.height / facs.naturalHeight;
	       var ratio = Math.min(rw, rh) * 0.95;
		  ctx.drawImage(facs, (canvas.width / 2 - (facs.naturalWidth * ratio) / 2), (canvas.height / 2 - (facs.naturalHeight * ratio) / 2) , facs.naturalWidth * ratio, facs.naturalHeight * ratio);
	   }
	   /* Redrawing create the canvas, clearing off anything else that
	    *  was there before (including the loading thing) */
	    redraw();
		/* And then get rid of the static image */
		/*image.style.display='none';*/

		var lastX=canvas.width/2, lastY=canvas.height/2;
		
		var dragStart,dragged;
		
		
		canvas.addEventListener('mousedown',function(evt){
			document.body.style.mozUserSelect = document.body.style.webkitUserSelect = document.body.style.userSelect = 'none';
			document.body.style.cursor = 'grabbing';
			lastX = evt.offsetX || (evt.pageX - canvas.offsetLeft);
			lastY = evt.offsetY || (evt.pageY - canvas.offsetTop);
			dragStart = ctx.transformedPoint(lastX,lastY);
			dragged = false;
		},false);
		canvas.addEventListener('mousemove',function(evt){
			lastX = evt.offsetX || (evt.pageX - canvas.offsetLeft);
			lastY = evt.offsetY || (evt.pageY - canvas.offsetTop);
			dragged = true;
			if (dragStart){
				var pt = ctx.transformedPoint(lastX,lastY);
				ctx.translate(pt.x-dragStart.x,pt.y-dragStart.y);
				redraw();
			}
		},false);
		canvas.addEventListener('mouseup',function(evt){
			dragStart = null;
			if (!dragged) zoom(evt.shiftKey ? -1 : 1 );
			document.body.style.cursor = 'auto';
			document.body.style.mozUserSelect = document.body.style.webkitUserSelect = document.body.style.userSelect = 'auto';
		},false);
		
		var highest = 13;
		var lowest = 0.85;
	
		var zoom = function(clicks){
		    var scaleFactor = 1.1;
			var pt = ctx.transformedPoint(lastX,lastY);
			ctx.translate(pt.x,pt.y);
			/* Quick way of determining whether or not we should disable zoom;
			 *  */
			if ((ctx.xform.a > highest  && clicks > 0) || (ctx.xform.a < lowest && clicks < 0)){
			    scaleFactor = 1;
			}
			var factor = Math.pow(scaleFactor,clicks);
			ctx.scale(factor,factor);
			ctx.translate(-pt.x,-pt.y);
			redraw();
		}


/* TO DO: FIx this across browsers: Safari and Chrome seem to use a different sort of 
 * scroll handler than Firefox and the delta seems quite odd indeed; */
		var handleScroll = function(evt){
		/* JT changed scroll delate from 40 to 750, since it was moving too quickly */
/*		    console.log(evt);
		    console.log(evt.wheelDelta);*/
			var delta = evt.wheelDelta ? evt.wheelDelta/400 : evt.detail ? -evt.detail/3.5 : 0;
			var direction = (evt.detail<0 || evt.wheelDelta>0) ? 1 : -1;
			if (delta) zoom(delta);
			return evt.preventDefault() && false;
		};
		
		
		canvas.addEventListener('DOMMouseScroll',handleScroll,true);
		canvas.addEventListener('mousewheel',handleScroll,true);
		canvas.addEventListener('MozMousePixelScroll', function(e){e.preventDefault()},false);
		
	};

  


	/* This function is where the difficult
	 * matrix algebra happens and has been left
	 * virtually untouched. All credit goes to the
	 * author.   */
	
	// Adds ctx.getTransform() - returns an SVGMatrix
	// Adds ctx.transformedPoint(x,y) - returns an SVGPoint
	
	
	function trackTransforms(ctx){
		var svg = document.createElementNS("http://www.w3.org/2000/svg",'svg');
		var xform = svg.createSVGMatrix();
		ctx.getTransform = function(){ return xform; };
		var savedTransforms = [];
		var save = ctx.save;
		
		ctx.save = function(){
			savedTransforms.push(xform.translate(0,0));
			ctx.xform = xform;
			return save.call(ctx);
		};
		
	
		
		var restore = ctx.restore;
		ctx.restore = function(){
			xform = savedTransforms.pop();
				ctx.xform = xform;
			return restore.call(ctx);
		};

		var scale = ctx.scale;
		ctx.scale = function(sx,sy){
		   xform = xform.scaleNonUniform(sx,sy);
		   	ctx.xform = xform;
		   return scale.call(ctx,sx,sy);
		};
		var rotate = ctx.rotate;
		ctx.rotate = function(radians){
			xform = xform.rotate(radians*180/Math.PI);
				ctx.xform = xform;
			return rotate.call(ctx,radians);
		};
		var translate = ctx.translate;
		ctx.translate = function(dx,dy){
			xform = xform.translate(dx,dy);
				ctx.xform = xform;
			return translate.call(ctx,dx,dy);
		};
		var transform = ctx.transform;
		ctx.transform = function(a,b,c,d,e,f){
			var m2 = svg.createSVGMatrix();
			m2.a=a; m2.b=b; m2.c=c; m2.d=d; m2.e=e; m2.f=f;
			xform = xform.multiply(m2);
         	ctx.xform = xform;
			return transform.call(ctx,a,b,c,d,e,f);
		};
		var setTransform = ctx.setTransform;
		ctx.setTransform = function(a,b,c,d,e,f){
			xform.a = a;
			xform.b = b;
			xform.c = c;
			xform.d = d;
			xform.e = e;
			xform.f = f;
				ctx.xform = xform;
			return setTransform.call(ctx,a,b,c,d,e,f);
		};

		var pt  = svg.createSVGPoint();
		ctx.transformedPoint = function(x,y){
			pt.x=x; pt.y=y;
			return pt.matrixTransform(xform.inverse());
			
		}
		
	
	}