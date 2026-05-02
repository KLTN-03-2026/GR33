#!/bin/bash
cd /Users/pong/Documents/Code/Project/GR33/smart_contracts

PRIVATE_KEY="0xac0974bec39a17e36ba4a6b4d238ff944bacb478cbed5efcae784d7bf4f2ff80"
OWNER_ADDRESS="0xf39Fd6e51aad88F6F4ce6aB8827279cffFb92266"
RPC_URL="http://127.0.0.1:8545"

echo "🔨 Đang biên dịch Smart Contract..."
/Users/pong/.foundry/bin/forge build --silent

echo "🚀 Deploy lên Anvil..."
/Users/pong/.foundry/bin/forge create \
    src/AcademicNFT.sol:AcademicNFT \
    --rpc-url $RPC_URL \
    --private-key $PRIVATE_KEY \
    --constructor-args $OWNER_ADDRESS \
    --json 2>&1 | python3 -c "
import sys, json
raw = sys.stdin.read()
start = raw.find('{')
end = raw.rfind('}')
if start != -1 and end != -1:
    try:
        obj = json.loads(raw[start:end+1])
        addr = obj.get('deployedTo', '')
        if addr:
            print('✅ Deploy thành công!')
            print('📦 Địa chỉ Contract: ' + addr)
            print()
            print('👉 Cập nhật vào .env Backend:')
            print('BLOCKCHAIN_CONTRACT_ADDRESS=' + addr)
        else:
            print('Dry run - chưa deploy thật. JSON:')
            print(json.dumps(obj, indent=2)[:300])
    except Exception as e:
        print('Parse error:', e)
        print(raw[:500])
else:
    print(raw[:500])
"
