var hexDigits = new Array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 

function rgb2hex(rgb)
{
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function hex(x)
{
    return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
}

function rotateObject(obj,angle)
{
    var resetOrigin = false;

    if (!obj) return;

    if ((obj.originX !== 'center' || obj.originY !== 'center') && obj.centeredRotation)
    {
        obj.setOriginToCenter && obj.setOriginToCenter();
        resetOrigin = true;
    }

    obj.setAngle(angle).setCoords();

    if (resetOrigin)
    {
        obj.setCenterToOrigin && obj.setCenterToOrigin();
    }
}

fabric.Object.prototype.setOriginToCenter = function ()
{
    this._originalOriginX = this.originX;
    this._originalOriginY = this.originY;

    var center = this.getCenterPoint();

    this.set({
        originX: 'center',
        originY: 'center',
        left: center.x,
        top: center.y
    });
};

fabric.Object.prototype.setCenterToOrigin = function ()
{
    var originPoint = this.translateToOriginPoint(
    this.getCenterPoint(),
    this._originalOriginX,
    this._originalOriginY);

    this.set({
        originX: this._originalOriginX,
        originY: this._originalOriginY,
        left: originPoint.x,
        top: originPoint.y
    });
};