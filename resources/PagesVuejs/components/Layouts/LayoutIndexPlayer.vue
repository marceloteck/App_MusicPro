<template>
<CapaAbertura type="time"/>
<Loading />
<NavBarPlayer />
    <div class="layoutPage">
        <div class="container-xl">
            <slot />
        </div>
    </div>
</template>

<style lang="css" scoped>
.layoutPage{
    margin-top: 60px;
}
</style>