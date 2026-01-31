<template>
<CapaAbertura type="time"/>
<Loading />
<NavBarMobile />
    <div class="layoutPage">
        <div class="container-xl">
            <slot />
        </div>
    </div>
</template>

<style lang="css" scoped>
.layoutPage{
    margin-top: 60px;
    display: flex;
    flex-direction: column;
    gap: 25px;
    margin-bottom: 70px;
}
</style>