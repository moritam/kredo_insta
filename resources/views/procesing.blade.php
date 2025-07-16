<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Particle Sound Visualizer</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.0/p5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.0/addons/p5.sound.min.js"></script>
    <style>
        body {
            margin: 0;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <script>
        let particles = [];
        let radius = 46;
        let moveMode = 1;
        let speed = 1.0;
        let beatCnt = 0;
        let enigmatics = 0;
        let song;
        let beatDetect;

        function preload() {
            song = loadSound('Radiohead - No-Suprises02.mp3'); // Ensure to host the audio file
        }

        function setup() {
            createCanvas(800, 400, WEBGL);
            frameRate(32);
            song.loop();
            beatDetect = new p5.AudioIn();
            beatDetect.start();
            initParticles();
        }

        function initParticles() {
            let particlesDensity = 18;
            let particleMargin = 64;

            for (let y = -particleMargin; y < height + particleMargin; y += particlesDensity) {
                for (let x = -particleMargin; x < width + particleMargin; x += particlesDensity) {
                    let theta = random(0, TWO_PI);
                    let u = random(-1, 1);
                    let c = color(50 + 50 * sin(PI * x / width), 127, 255 * sin(PI * y / width);
                    particles.push(new Particle(c, random(64), random(64), random(64), theta, u));
                }
            }
        }

        function draw() {
            background(0);
            updateParticles();
            drawParticles();
        }

        function updateParticles() {
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
            }
        }

        function drawParticles() {
            for (let i = 0; i < particles.length; i++) {
                particles[i].render();
            }
        }

        function mousePressed() {
            enigmatics++;
            if (enigmatics > 1) {
                enigmatics = -1;
            }
        }

        function switchMode() {
            moveMode = (moveMode === 0) ? 1 : 0;
            speed = (moveMode === 0) ? 0.01 : 0.05;
        }

        class Particle {
            constructor(c, nx, ny, nz, theta, u) {
                this.x = width / 2;
                this.y = height / 2;
                this.nextX = width / 2;
                this.nextY = height / 2;
                this.nextZ = width % height;
                this.theColor = c;
                this.theta = theta;
                this.u = u;
                this.vTheta = 0;
                this.vU = 0;
            }

            update() {
                this.vTheta = random(-0.001, 0.001);
                this.theta += this.vTheta;

                if (this.theta < 0 || this.theta > TWO_PI) {
                    this.theta *= -1;
                }

                this.vU += random(-0.001, 0.001);
                this.u += this.vU;
                if (this.u < -1 || this.u > 1) {
                    this.vU *= -1;
                }

                this.vU *= 0.95;
                this.vTheta *= 0.95;

                switch (moveMode) {
                    case 0: // Spreading
                        this.nextX += random(-width / 16, width / 16);
                        this.nextY += random(-height / 16, height / 16);
                        this.nextZ += random(-height / 16, height / 16);
                        break;

                    case 1: // Gathering
                        this.nextX = (radius * cos(this.theta) * sqrt(1 - (this.u * this.u)));
                        this.nextY = (radius * sin(this.theta) * sqrt(1 - (this.u * this.u)));
                        this.nextZ = this.u * radius;
                }

                this.x += (this.nextX - this.x) * speed;
                this.y += (this.nextY - this.y) * speed;
                this.z += (this.nextZ - this.z) * speed;
            }

            render() {
                stroke(this.theColor);
                point(this.x - width / 2, this.y - height / 2, this.z);
            }
        }
    </script>
</body>

</html>