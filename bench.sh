#!/bin/bash

# Configurações
REQUESTS=1000
CONCURRENCY=50

# Endpoints
FPM_URL="http://localhost:8080/user/balance/21"
REACTPHP_URL="http://localhost:8081/user/balance/21"

echo "🚀 Iniciando benchmark com $REQUESTS requisições e concorrência de $CONCURRENCY"

echo -e "\n🔵 PHP-FPM (Nginx) em $FPM_URL"
ab -n $REQUESTS -c $CONCURRENCY $FPM_URL/ > fpm_result.txt
tail -n 10 fpm_result.txt

echo -e "\n🟢 ReactPHP em $REACTPHP_URL"
ab -n $REQUESTS -c $CONCURRENCY $REACTPHP_URL/ > react_result.txt
tail -n 10 react_result.txt

echo -e "\n📊 Comparação final:"
echo "Tempo total FPM:    $(grep 'Time taken for tests' fpm_result.txt | awk '{print $5}') segundos"
echo "Tempo total React:  $(grep 'Time taken for tests' react_result.txt | awk '{print $5}') segundos"

echo "Req/s FPM:          $(grep 'Requests per second' fpm_result.txt | awk '{print $4}')"
echo "Req/s React:        $(grep 'Requests per second' react_result.txt | awk '{print $4}')"
