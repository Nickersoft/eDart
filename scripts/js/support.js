function convert_to_hex(val) 
{
    var parts = val.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);

    delete(parts[0]);

    for (var i = 1; i < 4; ++i) 
    {
        parts[i] = parseInt(parts[i]).toString(16);

        if(parts[i].length == 1)
        {
        	parts[i] = '0' + parts[i];
        }
    }

    var color = '#' + parts.join('');
    return color;
}