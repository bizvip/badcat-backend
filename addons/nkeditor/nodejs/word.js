/**
 * 代码来源：http://www.thinkphp.cn/topic/27567.html
 * 如需编译请使用 pkg -t node12-win word.js 命令编译成word.exe文件
 */
var service = {
    http: require('http'),
    https: require('https'),
    url: require('url'),
    querystring: require('querystring'),
    fs: require('fs'),
    os: require('os'),
    config: {
        timeout: 60000,
        charset: 'utf8',
        port: 10101, //https协议端口在原有端口上加1
        host: '127.0.0.1'
    },
    router: {
        index: function (res, query) {
            res.end('Server is running!');
        },
        check: function (res, query) {
            var result = {status: 1, info: 'success'};
            result = JSON.stringify(result);
            if (typeof query.callback == 'string') {
                result = query.callback + '(' + result + ')';
            }
            res.end(result);
        },
        word: function (res, query) {
            var _this = service;
            var result = {status: 0, index: query.index, info: 'error'};
            if (typeof query.file == 'string') {
                var img = query.file.match(/file:\/\/\/([a-z]\:)?(localhost)?(\S+\.(png|jpg|jpeg|gif|bmp))/i);
                if (img) {
                    var base64 = _this.base64_encode(img[3]);
                    if (base64) {
                        result.status = 1;
                        result.index = query.index;
                        result.info = 'data:image/' + img[4] + ';base64,' + base64;
                    }
                }
            }
            result = JSON.stringify(result);
            if (typeof query.callback == 'string') {
                result = query.callback + '(' + result + ')';
            }
            res.end(result);
        }
    },
    start: function () {
        var _this = this;
        var _app = function (req, res) {
            // 添加响应头
            // res.setHeader("Access-Control-Allow-Origin", "*");
            // res.setHeader("Access-Control-Allow-Private-Network", "true");

            var URL = _this.url.parse(req.url);
            var receive = [];
            var router = null;
            switch (URL.pathname) {
                case '/word':
                    router = _this.router.word;
                    break;
                case '/check':
                    router = _this.router.check;
                    break;
                default:
                    router = _this.router.index;
            }
            req.setEncoding(_this.config.charset);
            req.addListener('data', function (data) {
                receive.push(data);
            });
            res.writeHead(200, {'Content-Type': 'text/plain'});
            res.on("close", function () {
                console.log("res closed");
            });
            req.on("close", function () {
                console.log("req closed");
            });
            req.addListener('end', function () {
                router(res, _this.querystring.parse(URL.query));
            });
        };

        try {
            var Server = _this.http.createServer(_app);
            Server.listen(_this.config.port, _this.config.host, 1024);
            Server.setTimeout(_this.config.timeout, function (cli) {
                cli.end('timeout\n');
            });
            console.log('Http server running at http://' + _this.config.host + ':' + _this.config.port);
        } catch (e) {
            console.error(e.message);
            console.error('Unable to start Http server');
        }

        try {
            var privateKey = _this.fs.readFileSync('./certs/private.pem');
            var certificate = _this.fs.readFileSync('./certs/file.crt');
            var credentials = {key: privateKey, cert: certificate};

            var ServerSSL = _this.https.createServer(credentials, _app);

            var sslPort = parseInt(_this.config.port) + 1;
            ServerSSL.listen(sslPort, _this.config.host, 1024);
            ServerSSL.setTimeout(_this.config.timeout, function (cli) {
                cli.end('timeout\n');
            });
            console.log('Https server running at https://' + _this.config.host + ':' + sslPort);
        } catch (e) {
            console.error(e.message);
            console.error('Unable to start Https server');
        }
    },
    //base64
    base64_encode: function (file) {
        if (!file) {
            return '';
        }
        if (!file.match(/^([a-z]{1,2}\:\/|\/)/i)) {
            file = "/" + file;
        }
        try {
            file = decodeURIComponent(file);
            if (this.os.type() == 'Darwin' || this.os.type() == 'Linux') {
                file = file.replace(/\s/, '\ ');
            }
            var bitmap = this.fs.readFileSync(file);
        } catch (e) {
            console.log(e.message);
            return '';
        }
        return new Buffer.from(bitmap).toString('base64');
    }
};
service.start();

