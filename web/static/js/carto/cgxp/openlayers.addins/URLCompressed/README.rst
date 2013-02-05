Transfrom geometries or features to be able to put them in URL parameters.

For example::

    new OpenLayers.Format.URLCompressed().write(
        new OpenLayers.Feature.Vector(
            new OpenLayers.Geometry.Point(10, 20),
            {name: "Test"}, {fillColor: "green"}
        )
    );

return::

    p(P9-~name*Test~fillColor*green)

 for a multipolygon:

    new OpenLayers.Format.URLCompressed().write(
        new OpenLayers.Geometry.MultiPolygon([
            new OpenLayers.Geometry.Polygon([
                new OpenLayers.Geometry.LinearRing([
                    new OpenLayers.Geometry.Point(10, 20),
                    new OpenLayers.Geometry.Point(30, 40),
                    new OpenLayers.Geometry.Point(10, 20)
                ]),
                new OpenLayers.Geometry.LinearRing([
                    new OpenLayers.Geometry.Point(50, 60),
                    new OpenLayers.Geometry.Point(70, 80),
                    new OpenLayers.Geometry.Point(50, 60)
                ])
            ]),
            new OpenLayers.Geometry.Polygon([
                new OpenLayers.Geometry.LinearRing([
                    new OpenLayers.Geometry.Point(15, 25),
                    new OpenLayers.Geometry.Point(35, 45),
                    new OpenLayers.Geometry.Point(15, 25)
                ]),
                new OpenLayers.Geometry.LinearRing([
                    new OpenLayers.Geometry.Point(55, 65),
                    new OpenLayers.Geometry.Point(75, 85),
                    new OpenLayers.Geometry.Point(55, 65)
                ])
            ])
        ])
    );

return::

    A((P9-9-9-8-8-'h_h_9-9-8-8-)(6_6_9-9-8-8-'h_h_9-9-8-8-))


To get small values event if the coordinates are bigger we encode the
differences between points, for example::

    new OpenLayers.Format.URLCompressed().write([
        new OpenLayers.Feature.Vector(
            new OpenLayers.Geometry.Point(1000000, 2000000)
        )
    ]);
    new OpenLayers.Format.URLCompressed().write([
        new OpenLayers.Feature.Vector(
            new OpenLayers.Geometry.Point(1000000, 2000000)
        ),
        new OpenLayers.Feature.Vector(
            new OpenLayers.Geometry.Point(1000010, 2000020)
        )
    ]);

return::

    Fp(152x-193u!)
    Fp(152x-193u!)p(P9-)

We can see that the second point (``p(P9-)``) is really smaller.
