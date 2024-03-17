import { Chains, createWeb3Auth } from "@kolirt/vue-web3-auth";
import { createApp } from "vue/dist/vue.esm-bundler.js";
import Wallet from "../js/components/Wallet.vue";

const app = createApp(Wallet);

app.use(
    createWeb3Auth({
        projectId: "d6eb491145ddbafe8af894199f6ff961",
        chains: [Chains.mainnet, Chains.sepolia, Chains.polygon],
    })
);

app.mount("#app");
