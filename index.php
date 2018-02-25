<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Game</title>
        
        <style>
            *{
			    margin: 0;
			
			}
			
			canvas{
				
            }
			body{
				height: 100vh; 
				display: flex;
				justify-content: center;
				align-items: center;
			}
        </style>
    </head>
    <body>
		
        <script>
            // variaveis do game
            var canvas, ctx, altura, largura, frames=0, maxPulos = 2, velocidade = 6, 
            estadoAtual, record,
            
            estados = {
                jogar: 0,
                jogando: 1,     //status do game
                perdeu: 2
            },
            chao = {
                y: 550,
                altura: 50,         // classes no javascript para o chao.       
                cor: "#ffdf70",                 
                
                desenha: function(){
                    ctx.fillStyle = this.cor;
                    ctx.fillRect(0, this.y, largura, this.altura);         //bagui pro metodo da variavel chao do mapa 
                }
            },
                    
            bloco = {
                x: 50,
                y: 0,
                altura: 50,                          //tudo isso pra fazer o bloco q pula
                largura: 50,
                cor: "#ff4e4e",
                gravidade: 1.6,   //somar a velocidade
                velocidade: 0,
                forcaDoPulo: 23.6,
                qntPulos: 0,
                score: 0,     //conta os pontos
                
                atualiza: function(){
                    this.velocidade += this.gravidade;      //atualiza a velocidade de gravidade do bloco
                    this.y += this.velocidade;
                    
                    if(this.y > chao.y - this.altura && estadoAtual != estados.perdeu){  //pa o bloco n passar do chao
                       this.y = chao.y - this.altura    //forço ele a parar no chao
                       this.qntPulos = 0;  //se o bloco cair no chao, reset os pulos
                       this.velocidade = 0; //PRA ELE PARAR DE INCREMENTAR VELOCIDADE QUANDO ESTIVER NO CHAO PARADO
                    }
                },
                
                pula: function(){
                if(this.qntPulos < maxPulos){    //so pula se a quantidade de pulos for menor que a qtd de pulos disponiveis
                    this.velocidade = -this.forcaDoPulo;      //pa pular, é so colocar ele contra a gravidade
                    this.qntPulos++;
                }
            },
            
                reset: function(){
                     this.velocidade = 0;  //reset na velocidade para nao voltar a incrementar parado
                     this.y = 0; //animação para ele cair do ceu
                     
                     if(this.score > record){
                         localStorage.setItem("record", this.score);
                         record = this.score;
                     }
                     
                     this.score = 0;
                },
                
                desenha: function(){
                    ctx.fillStyle = this.cor;
                    ctx.fillRect(this.x, this.y, this.altura, this.largura);      //isso pra metodos da variavel do bloco
                }
            },
                    obstaculos = {
                        vetor_obs: [], //vetor para os obstaculos
                        cores: ["#ffbc1c", "#ff1c1c", "#ff85e1", "#52a7ff", "#78ff5d"], //vetor para as cores dos obstaculos
                        TempoInsere:  0, //para n deixar o tempo dos obstaculos previsivel 
                        
                        insere: function(){
                            this.vetor_obs.push({  //push insere um dado no vetor e retorna o tamanho do vetor
                                x: largura,
                                //largura: 30 + Math.floor(21 *Math.random()),
                                largura: 50,
                                altura: 30 + Math.floor(120 *Math.random()),
                                cor: this.cores[Math.floor(5*Math.random())]  //random das cores 
                            });
                            this.TempoInsere = 40 + Math.floor(41 * Math.random());
                        },
                        
                          atualiza: function(){
                              if(this.TempoInsere == 0){
                                  this.insere();
                              }else{
                                  this.TempoInsere--;
                              }
                              for(var i = 0, tamanho = this.vetor_obs.length; i < tamanho; i++){
                                  var obs    = this.vetor_obs[i];  //pegando o obstaculo esolhido no for
                                  obs.x -= velocidade;         //velocidade do game com os osbstaculos(decrementacao da posicao dos obstaculos)
                                  
                                      if(bloco.x < obs.x + obs.largura && bloco.x + bloco.largura >=  //*   COLISAO   *
                                              obs.x && bloco.y + bloco.altura >= chao.y - obs.altura)
                                          estadoAtual = estados.perdeu;
                                      
                                      else if(obs.x == 0)
                                             bloco.score++;
                                      
                                      else if(obs.x <= -obs.largura){  //apaga a posicao depois q passa do canvas
                                      this.vetor_obs.splice(i, 1);  //splice serve para tirar um elemento do vetor
                                      tamanho--; //corrigi o erro de tentar acessar a posicao 0 inexistente
                                      i--;       //corrigi o erro de tentar acessar a posicao 0 inexistente
                                  }
                            }
                          },
                          
                          limpa: function()  { //limpa o array para reinicia- lo
                              this.vetor_obs = [];
                          },             
                          
                          
                          
                          
                          desenha: function(){
                              for(var i = 0, tamanho = this.vetor_obs.length; i < tamanho; i++){
                                  var obs = this.vetor_obs[i];
                                  ctx.fillStyle = obs.cor;
                                  ctx.fillRect(obs.x, chao.y - obs.altura, obs.largura, obs.altura);
                              }
                          }
                    };
            
            function clique(event){
                
                    if(estadoAtual == estados.jogando)  //normal se estiver jogando
                        bloco.pula();       //ativa o pulo no clique do mouse
                    
                    else if(estadoAtual == estados.jogar){ //se for pra jogar, ele se torna normal
                        estadoAtual = estados.jogando;
                    }
                        
		   else if(estadoAtual == estados.perdeu){
                       estadoAtual = estados.jogar; //se ele perdeu e ele clicar dnv, ele comeca a jogar dnv
                       obstaculos.limpa();
                       bloco.reset();  //reset
                       
                   }		
            }
            
            
            
            function main(){
                altura = window.innerHeight; // devolve a altura da janela do usuario 
                largura = window.innerWidth; // devolve a largura da janela do usuario 
                
                if(largura >= 500){
                    largura = 600;
                    altura = 600;
                }
                
                canvas = document.createElement("canvas"); // criando o tal do canvas
                canvas.width = largura;
                canvas.height = altura;  //criando canvas e definindo a altura e largura
                canvas.style.border = "1px solid #000"; // criando borda preta pro canvas
                
                ctx = canvas.getContext("2d");
                document.body.appendChild(canvas);
                
                document.addEventListener("mousedown", clique);//verifica o clique
                document.addEventListener('keypress', function (e){
					if(e.keyCode == 32){
						clique();
					}
				});
                estadoAtual = estados.jogar;  //telinha para iniciar o jogo
		record = localStorage.getItem("record"); //vai procurar pela variavel record no jogo
                
                if(record == null)
                    record = 0;
				
                roda();
                
            }
            function roda(){
                atualiza();
                desenha();
                window.requestAnimationFrame(roda); //atualizar e rodar o frame da animacao
            }
            function atualiza(){
                frames++;
                bloco.atualiza(); //chama o metodo para atualizar a posicao do bloco
                
                if(estadoAtual == estados.jogando) //so atualiza os blocos se estiver jogando
                  obstaculos.atualiza(); //atualiza os obstaculos no jogo  
              
                //else if(estadoAtual == estados.perdeu)
                  
            }
            function desenha(){
                ctx.fillStyle = "#50beff";
                ctx.fillRect(0, 0, largura, altura); // cor no canvas
                
                ctx.fillStyle = "black";
                ctx.font = "50px Arial";
                ctx.fillText(bloco.score, 30, 68);
                
                if(estadoAtual == estados.jogar){
                    ctx.fillStyle = "green"; //bloco iniciar o game em verde
                    ctx.fillRect(largura / 2 - 50, altura / 2 - 50, 100, 100);
                }
                else if (estadoAtual == estados.perdeu){
                    ctx.fillStyle = "red"; //bloco que aparece quando vc perde o jogo 
                    ctx.fillRect(largura / 2 - 50, altura / 2 - 50, 100, 100);
                    
                    ctx.save();
                    ctx.translate(largura / 2, altura / 2);
                    ctx.fillStyle = "black";
                    
                    if(bloco.score > record)
                        ctx.fillText("Novo Record!", -150, -65);   //NOVO RECORD
                    
                    else if(record < 10)
                        ctx.fillText("Record "+ record, -99, -65);  //NOVO RECORD CASO ELE TENHA 2 DIGITOS
                    
                    else if(record >= 10 && record < 100)
                        ctx.fillText("Record "+ record, -112, -65); //NOVO RECORD CASO ELE TENHA 3 DIGITOS
                    else
                        ctx.fillText("Record "+ record, -125, -65); //NOVO RECORD CASO ELE TENHA 4 DIGITOS
                        
                         if(bloco.score < 10)
                         ctx.fillText(bloco.score, -13, 19);
                    
                         else if(bloco.score >= 10 && bloco.score < 100)
                         ctx.fillText(bloco.score, -26, 19);  
                     
                         else
                         ctx.fillText(bloco.score, -39, 19);
                                    
                        ctx.restore();
                
                }
                
                    
                
                
                else if(estadoAtual == estados.jogando)
                    obstaculos.desenha();
                chao.desenha(); //acessando a variavel chao e o seu metodo.
                bloco.desenha(); //acessando a variavel bloco e o seu metodo.
            
        }
                
            //inicializa o game
            main();
        </script>
        
    </body>
    
</html>